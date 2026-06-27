<?php

namespace App\Http\Controllers;

use App\Models\ContentSlider;
use App\Models\Berita;
use App\Models\Album;
use App\Models\ActivityLog;
use Inertia\Inertia;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use Exception;

class ContentSliderController extends Controller
{
    /**
     * Convert uploaded image to WebP format
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string The path to the converted WebP image
     */

    private function convertToWebP($file, $directory = 'content-sliders')
    {
        try {
            $originalExtension = strtolower($file->getClientOriginalExtension());
            
            // If already WebP, just store it
            if ($originalExtension === 'webp') {
                $path = $file->store($directory, 'public');
                return 'storage/' . $path;
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
                Log::warning('Failed to create image resource for WebP conversion', [
                    'original_extension' => $originalExtension,
                    'temp_path' => $fullTempPath,
                ]);
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

            // Convert to WebP with quality 85 (good balance between quality and size)
            $success = imagewebp($image, $fullWebpPath, 85);

            // Free up memory
            imagedestroy($image);
            
            // Delete temporary file
            Storage::disk('public')->delete($tempPath);

            if ($success) {
                Log::info('Image successfully converted to WebP', [
                    'original_extension' => $originalExtension,
                    'original_size' => $file->getSize(),
                    'webp_path' => $webpPath,
                    'webp_size' => filesize($fullWebpPath),
                    'size_reduction' => round((1 - filesize($fullWebpPath) / $file->getSize()) * 100, 2) . '%',
                ]);
                return 'storage/' . $webpPath;
            } else {
                Log::warning('Failed to save WebP image, using original', [
                    'original_extension' => $originalExtension,
                ]);
                $path = $file->store($directory, 'public');
                return 'storage/' . $path;
            }
        } catch (Exception $e) {
            Log::error('Error converting image to WebP', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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

        $query = ContentSlider::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%');
            });
        }

        // Determine sort field and direction from query with safe defaults
        $sortBy = $request->query('sort_by', 'updated_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // Whitelist supported columns to prevent invalid queries
        $allowedSorts = ['title', 'updated_at', 'created_at', 'published'];
        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'updated_at';
        }

        // Handle per-page pagination with safe whitelist
        $perPage = (int) $request->query('per_page', 10);
        $allowedPerPage = [5, 10, 20, 50];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $sliders = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

        // Get the first 5 published sliders (ordered by updated_at desc) for highlighting
        $activeSliderIds = ContentSlider::where('published', true)
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->pluck('id')
            ->toArray();

        $data = [
            'data' => $sliders->getCollection()->transform(function ($s, $index) use ($activeSliderIds) {
                return [
                    'id' => $s->id,
                    'title' => $s->title,
                    'image' => $s->image,
                    'image_url' => $s->image_url,
                    'link' => $s->link,
                    'published' => (bool) $s->published,
                    'updated_at' => $s->updated_at?->toDateTimeString(),
                    'is_active_highlight' => in_array($s->id, $activeSliderIds),
                ];
            }),
            'links' => [
                'first' => $sliders->url(1),
                'last' => $sliders->url($sliders->lastPage()),
                'prev' => $sliders->previousPageUrl(),
                'next' => $sliders->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $sliders->currentPage(),
                'last_page' => $sliders->lastPage(),
                'per_page' => $sliders->perPage(),
                'total' => $sliders->total(),
            ],
        ];

        return Inertia::render('ContentSliders/Index', [
            'sliders' => $data,
            'filters' => [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $beritas = Berita::latest('updated_at')->get(['id','title','slug']);
        $albums = Album::latest('updated_at')->get(['id','title']);

        return Inertia::render('ContentSliders/Create', [
            'beritas' => $beritas,
            'albums' => $albums,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            Log::info('Creating new content slider', [
                'user_id' => Auth::id() ?? 'guest',
                'title' => $request->input('title'),
            ]);

            $rules = [
                'title' => ['required', 'array'],
                'title.id' => ['required', 'string', 'max:255'],
                'title.en' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'array'],
                'description.id' => ['nullable', 'string'],
                'description.en' => ['nullable', 'string'],
                'link' => ['nullable', 'string', 'max:2048'],
                'published' => ['required', 'boolean'],
            ];

            if ($request->hasFile('image')) {
                $rules['image'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'];

                Log::debug('Processing uploaded image file', [
                    'original_name' => $request->file('image')->getClientOriginalName(),
                    'size' => $request->file('image')->getSize(),
                    'mime' => $request->file('image')->getMimeType(),
                ]);
            } else {
                $rules['image'] = ['required', 'string', function ($attribute, $value, $fail) {
                    if (!preg_match('/^(https?:\/\/|.+\.(jpg|jpeg|png|gif|webp|svg)$)/i', $value)) {
                        $fail('Gambar harus berupa URL yang valid atau path gambar.');
                    }
                }];
                Log::debug('Processing image URL', ['url' => $request->input('image')]);
            }

            $messages = [
                // Title
                'title.required' => 'Judul slider wajib diisi.',
                'title.array' => 'Format judul tidak valid.',
                'title.id.required' => 'Judul dalam Bahasa Indonesia wajib diisi.',
                'title.id.string' => 'Judul harus berupa teks.',
                'title.id.max' => 'Judul tidak boleh lebih dari 255 karakter.',
                'title.en.string' => 'Judul dalam Bahasa Inggris harus berupa teks.',
                'title.en.max' => 'Judul dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
                
                // Description
                'description.array' => 'Format deskripsi tidak valid.',
                'description.id.string' => 'Deskripsi harus berupa teks.',
                'description.en.string' => 'Deskripsi dalam Bahasa Inggris harus berupa teks.',
                
                // Image
                'image.required' => 'Gambar slider wajib diunggah.',
                'image.image' => 'File yang diunggah harus berupa gambar.',
                'image.mimes' => 'Gambar harus berformat JPEG, PNG, JPG, atau WebP.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
                'image.string' => 'URL gambar harus berupa teks yang valid.',
                
                // Link
                'link.string' => 'Link harus berupa teks.',
                'link.max' => 'Link tidak boleh lebih dari 2048 karakter.',
                
                // Published
                'published.required' => 'Status publikasi wajib dipilih.',
                'published.boolean' => 'Status publikasi harus berupa ya atau tidak.',
            ];

            $validated = $request->validate($rules, $messages);

            // Handle image as either uploaded file or URL/string
            if ($request->hasFile('image')) {
                $validated['image'] = $this->convertToWebP($request->file('image'));
                Log::info('Image file stored successfully', ['path' => $validated['image']]);
            } else {
                $validated['image'] = $request->input('image');
            }

            // Ensure title/description arrays at least contain id locale
            if (is_string($request->input('title'))) {
                $validated['title'] = ['id' => $request->input('title')];
            }
            if (is_string($request->input('description'))) {
                $validated['description'] = ['id' => $request->input('description')];
            }

            // Persist link if provided
            if ($request->filled('link')) {
                $validated['link'] = $request->input('link');
            }

            $validated['published'] = (bool)($validated['published'] ?? true);

            // Track creator/updater
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();

            $slider = ContentSlider::create($validated);

            Log::info('Content slider created successfully', [
                'slider_id' => $slider->id,
                'title' => $slider->title,
                'published' => $slider->published,
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Created ContentSlider: ' . ($slider->title ?? 'Untitled') . ' (ID: ' . $slider->id . ')',
            ]);

            return redirect()->route('content-sliders.index')
                ->with('status', 'Slider created successfully.');
        } catch (ValidationException $e) {

            Log::warning('Validation failed when creating content slider', [
                'errors' => $e->errors(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {

            Log::error('Error creating content slider', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while creating the slider: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ContentSlider $content_slider)
    {
        // Load relationships
        $content_slider->load(['createdBy', 'updatedBy']);

        return Inertia::render('ContentSliders/Show', [
            'slider' => [
                'id' => $content_slider->id,
                'title' => $content_slider->title,
                'title_translations' => $content_slider->getTranslations('title'),
                'description' => $content_slider->description,
                'description_translations' => $content_slider->getTranslations('description'),
                'image' => $content_slider->image,
                'link' => $content_slider->link,
                'published' => (bool) $content_slider->published,
                'created_at' => $content_slider->created_at?->toDateTimeString(),
                'updated_at' => $content_slider->updated_at?->toDateTimeString(),
                'image_url' => $content_slider->image_url,
                'created_by' => $content_slider->createdBy ? [
                    'id' => $content_slider->createdBy->id,
                    'name' => $content_slider->createdBy->name,
                ] : null,
                'updated_by' => $content_slider->updatedBy ? [
                    'id' => $content_slider->updatedBy->id,
                    'name' => $content_slider->updatedBy->name,
                ] : null,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContentSlider $content_slider)
    {
        $beritas = Berita::latest('updated_at')->get(['id','title','slug']);
        $albums = Album::latest('updated_at')->get(['id','title']);

        return Inertia::render('ContentSliders/Edit', [
            'slider' => [
                'id' => $content_slider->id,
                'title' => $content_slider->title,
                'title_translations' => $content_slider->getTranslations('title'),
                'description' => $content_slider->description,
                'description_translations' => $content_slider->getTranslations('description'),
                'image' => $content_slider->image,
                'image_url' => $content_slider->image_url,
                'link' => $content_slider->link,
                'published' => (bool) $content_slider->published,
            ],
            'beritas' => $beritas,
            'albums' => $albums,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContentSlider $content_slider)
    {
        try {
            Log::info('Updating content slider', [
                'user_id' => Auth::id() ?? 'guest',
                'slider_id' => $content_slider->id,
                'title' => $request->input('title'),
            ]);

            $rules = [
                'title' => ['required', 'array'],
                'title.id' => ['required', 'string', 'max:255'],
                'title.en' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'array'],
                'description.id' => ['nullable', 'string'],
                'description.en' => ['nullable', 'string'],
                'link' => ['nullable', 'string', 'max:2048'],
                'published' => ['nullable', 'boolean'],
            ];

            if ($request->hasFile('image')) {

                $rules['image'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'];
                Log::debug('Processing uploaded image file for update', [
                    'original_name' => $request->file('image')->getClientOriginalName(),
                    'size' => $request->file('image')->getSize(),
                    'mime' => $request->file('image')->getMimeType(),
                ]);

            } else if ($request->filled('image')) {

                $rules['image'] = ['required', 'string', function ($attribute, $value, $fail) {
                    if (!preg_match('/^(https?:\/\/|.+\.(jpg|jpeg|png|gif|webp|svg)$)/i', $value)) {
                        $fail('Gambar harus berupa URL yang valid atau path gambar.');
                    }
                }];
                Log::debug('Processing image URL for update', ['url' => $request->input('image')]);
            }

            $messages = [
                // Title
                'title.required' => 'Judul slider wajib diisi.',
                'title.array' => 'Format judul tidak valid.',
                'title.id.required' => 'Judul dalam Bahasa Indonesia wajib diisi.',
                'title.id.string' => 'Judul harus berupa teks.',
                'title.id.max' => 'Judul tidak boleh lebih dari 255 karakter.',
                'title.en.string' => 'Judul dalam Bahasa Inggris harus berupa teks.',
                'title.en.max' => 'Judul dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
                
                // Description
                'description.array' => 'Format deskripsi tidak valid.',
                'description.id.string' => 'Deskripsi harus berupa teks.',
                'description.en.string' => 'Deskripsi dalam Bahasa Inggris harus berupa teks.',
                
                // Image
                'image.required' => 'Gambar slider wajib diunggah.',
                'image.image' => 'File yang diunggah harus berupa gambar.',
                'image.mimes' => 'Gambar harus berformat JPEG, PNG, JPG, atau WebP.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
                'image.string' => 'URL gambar harus berupa teks yang valid.',
                
                // Link
                'link.string' => 'Link harus berupa teks.',
                'link.max' => 'Link tidak boleh lebih dari 2048 karakter.',
                
                // Published
                'published.boolean' => 'Status publikasi harus berupa ya atau tidak.',
            ];

            $validated = $request->validate($rules, $messages);
            
            if ($request->hasFile('image')) {
                $validated['image'] = $this->convertToWebP($request->file('image'));
                Log::info('Image file updated successfully', ['path' => $validated['image']]);
            } else if ($request->filled('image')) {
                $validated['image'] = $request->input('image');
            }

            // Ensure arrays exist if strings were sent
            if (is_string($request->input('title'))) {
                $validated['title'] = ['id' => $request->input('title')];
            }
            if (is_string($request->input('description'))) {
                $validated['description'] = ['id' => $request->input('description')];
            }

            // Persist link if provided
            if ($request->filled('link')) {
                $validated['link'] = $request->input('link');
            }

            $validated['published'] = (bool)($validated['published'] ?? true);

            // Track updater
            $validated['updated_by'] = Auth::id();

            $content_slider->update($validated);

            Log::info('Content slider updated successfully', [
                'slider_id' => $content_slider->id,
                'title' => $content_slider->title,
                'published' => $content_slider->published,
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Updated ContentSlider: ' . ($content_slider->title ?? 'Untitled') . ' (ID: ' . $content_slider->id . ')',
            ]);

            return redirect()->route('content-sliders.index')
                ->with('status', 'Slider updated successfully.');
        } catch (ValidationException $e) {

            Log::warning('Validation failed when updating content slider', [
                'slider_id' => $content_slider->id,
                'errors' => $e->errors(),
                'user_id' => Auth::id() ?? 'guest',
            ]);
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {

            Log::error('Error updating content slider', [
                'slider_id' => $content_slider->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while updating the slider: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContentSlider $content_slider)
    {
        try {
            Log::info('Deleting content slider', [
                'user_id' => Auth::id() ?? 'guest',
                'slider_id' => $content_slider->id,
                'title' => $content_slider->title,
            ]);

            $content_slider->delete();

            Log::info('Content slider deleted successfully', [
                'slider_id' => $content_slider->id,
                'title' => $content_slider->title,
            ]);

            // Write activity log (user, activity, date via created_at)
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Deleted ContentSlider: ' . ($content_slider->title ?? 'Untitled') . ' (ID: ' . $content_slider->id . ')',
            ]);

            return redirect()->route('content-sliders.index')
                ->with('status', 'Slider deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting content slider', [
                'slider_id' => $content_slider->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the slider: ' . $e->getMessage());
        }
    }
}
