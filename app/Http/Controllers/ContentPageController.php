<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class ContentPageController extends Controller
{
    /**
     * Convert uploaded image to WebP format
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string The path to the converted WebP image
     */
    private function convertToWebP($file, $directory = 'content-pages')
    {
        try {
            $originalExtension = strtolower($file->getClientOriginalExtension());
            
            // If already WebP, just store it
            if ($originalExtension === 'webp') {
                $path = $file->store($directory, 'public');
                return 'storage/' . $path;
            }

            // Increase memory limit for large images
            $currentLimit = ini_get('memory_limit');
            if (preg_match('/^(\d+)(.)$/', $currentLimit, $matches)) {
                $currentBytes = $matches[1] * ($matches[2] == 'G' ? 1073741824 : ($matches[2] == 'M' ? 1048576 : 1024));
                if ($currentBytes < 256 * 1048576) { // Less than 256MB
                    @ini_set('memory_limit', '256M');
                }
            }

            // Create a temporary path for the original file
            $tempPath = $file->store('temp', 'public');
            $fullTempPath = storage_path('app/public/' . $tempPath);

            // Create GD image resource from the uploaded file
            $image = null;
            switch ($originalExtension) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($fullTempPath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($fullTempPath);
                    // Preserve transparency
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($fullTempPath);
                    break;
                default:
                    // Unsupported format, just store as is
                    Storage::disk('public')->delete($tempPath);
                    $path = $file->store($directory, 'public');
                    return 'storage/' . $path;
            }

            if (!$image) {
                Storage::disk('public')->delete($tempPath);
                $path = $file->store($directory, 'public');
                return 'storage/' . $path;
            }

            // Generate unique filename for WebP
            $filename = uniqid() . '_' . time() . '.webp';
            $webpPath = $directory . '/' . $filename;
            $fullWebpPath = storage_path('app/public/' . $webpPath);

            // Ensure directory exists
            $webpDir = dirname($fullWebpPath);
            if (!file_exists($webpDir)) {
                mkdir($webpDir, 0755, true);
            }

            // Get original dimensions
            $width = imagesx($image);
            $height = imagesy($image);
            
            // Resize if image is too large (max 2000px on longest side)
            $maxDimension = 2000;
            if ($width > $maxDimension || $height > $maxDimension) {
                if ($width > $height) {
                    $newWidth = $maxDimension;
                    $newHeight = (int)(($height / $width) * $maxDimension);
                } else {
                    $newHeight = $maxDimension;
                    $newWidth = (int)(($width / $height) * $maxDimension);
                }
                
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                
                // Preserve transparency for PNG
                if ($originalExtension === 'png') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                    imagefill($resized, 0, 0, $transparent);
                }
                
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
            }

            // Convert to WebP with quality 85
            $success = imagewebp($image, $fullWebpPath, 85);

            // Free up memory
            imagedestroy($image);
            
            // Delete temporary file
            Storage::disk('public')->delete($tempPath);
            
            // Force garbage collection
            gc_collect_cycles();

            if ($success) {
                return 'storage/' . $webpPath;
            } else {
                $path = $file->store($directory, 'public');
                return 'storage/' . $path;
            }
        } catch (Exception $e) {
            Log::error('Error converting image to WebP', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            // Fallback: store original image
            $path = $file->store($directory, 'public');
            return 'storage/' . $path;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = ContentPage::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%')
                  ->orWhere('subtitle', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('description2', 'like', '%' . $search . '%');
            });
        }

        $pages = $query->latest()->orderBy('created_at')->paginate(10)->withQueryString();

        $data = [
            'data' => $pages->getCollection()->transform(function ($p) {
                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'slug' => $p->slug,
                    'published' => (bool) $p->published,
                    'updated_at' => $p->updated_at?->toDateTimeString(),
                ];
            }),
            'links' => [
                'first' => $pages->url(1),
                'last' => $pages->url($pages->lastPage()),
                'prev' => $pages->previousPageUrl(),
                'next' => $pages->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $pages->currentPage(),
                'last_page' => $pages->lastPage(),
                'per_page' => $pages->perPage(),
                'total' => $pages->total(),
            ],
        ];

        return Inertia::render('ContentPages/Index', [
            'pages' => $data,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('ContentPages/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description2' => ['nullable', 'string'],
            'metadata_title' => ['nullable', 'string', 'max:255'],
            'metadata_keywords' => ['nullable', 'string', 'max:255'],
            'metadata_description' => ['nullable', 'string', 'max:255'],
            'upload_files' => ['nullable', 'string'],
            'published' => ['nullable', 'boolean'],
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'];
        } else {
            $rules['image'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->convertToWebP($request->file('image'));
        }

        $validated['published'] = (bool)($validated['published'] ?? true);
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $contentPage = ContentPage::create($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Created content page: ' . $contentPage->title,
        ]);

        return redirect()->route('content-pages.index')->with('status', 'Content page created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ContentPage $content_page)
    {
        return Inertia::render('ContentPages/Show', [
            'page' => [
                'id' => $content_page->id,
                'title' => $content_page->title,
                'slug' => $content_page->slug,
                'subtitle' => $content_page->subtitle,
                'description' => $content_page->description,
                'description2' => $content_page->description2,
                'image' => $content_page->image,
                'metadata_title' => $content_page->metadata_title,
                'metadata_keywords' => $content_page->metadata_keywords,
                'metadata_description' => $content_page->metadata_description,
                'upload_files' => $content_page->upload_files,
                'published' => (bool) $content_page->published,
                'created_at' => $content_page->created_at?->toDateTimeString(),
                'updated_at' => $content_page->updated_at?->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContentPage $content_page)
    {
        return Inertia::render('ContentPages/Edit', [
            'page' => [
                'id' => $content_page->id,
                'title' => $content_page->title,
                'slug' => $content_page->slug,
                'subtitle' => $content_page->subtitle,
                'description' => $content_page->description,
                'description2' => $content_page->description2,
                'image' => $content_page->image,
                'metadata_title' => $content_page->metadata_title,
                'metadata_keywords' => $content_page->metadata_keywords,
                'metadata_description' => $content_page->metadata_description,
                'upload_files' => $content_page->upload_files,
                'published' => (bool) $content_page->published,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContentPage $content_page)
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description2' => ['nullable', 'string'],
            'metadata_title' => ['nullable', 'string', 'max:255'],
            'metadata_keywords' => ['nullable', 'string', 'max:255'],
            'metadata_description' => ['nullable', 'string', 'max:255'],
            'upload_files' => ['nullable', 'string'],
            'published' => ['nullable', 'boolean'],
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'];
        } else {
            $rules['image'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->convertToWebP($request->file('image'));
        }

        $validated['published'] = (bool)($validated['published'] ?? true);
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $content_page->update($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Updated content page: ' . $content_page->title,
        ]);

        return redirect()->route('content-pages.index')->with('status', 'Content page updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContentPage $content_page)
    {
        $contentPageTitle = $content_page->title;
        $content_page->delete();
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Deleted content page: ' . $contentPageTitle,
        ]);
        
        return redirect()->route('content-pages.index')->with('status', 'Content page deleted.');
    }

    /**
     * Public: Display content page by full slug path.
     */
    public function showPublicBySlug(\Illuminate\Http\Request $request, string $slug)
    {
        try {
            $page = \App\Models\ContentPage::where('slug', $slug)->where('published', 1)->first();
        } catch (\Throwable $e) {
            return \Inertia\Inertia::render('Error404');
        }

        if (!$page) {
            return \Inertia\Inertia::render('Error404');
        }

        return \Inertia\Inertia::render('ContentPages/ShowPublic', [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'subtitle' => $page->subtitle,
                'description' => $page->description,
                'description2' => $page->description2,
                'image' => $page->image,
                'metadata_title' => $page->metadata_title,
                'metadata_keywords' => $page->metadata_keywords,
                'metadata_description' => $page->metadata_description,
                'upload_files' => $page->upload_files,
                'published' => (bool) $page->published,
                'created_at' => $page->created_at?->toDateTimeString(),
                'updated_at' => $page->updated_at?->toDateTimeString(),
            ],
        ]);
    }
}