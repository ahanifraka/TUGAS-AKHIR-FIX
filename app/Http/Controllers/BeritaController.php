<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\BeritaCategory;
use App\Models\ActivityLog;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Validation\ValidationException;

class BeritaController extends Controller
{

    /**
     * Convert uploaded image to WebP format
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string The path to the converted WebP image
     */
    private function convertToWebP($file, $directory = 'beritas')
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
                
                Log::info('Resized large berita image', [
                    'original' => "{$width}x{$height}",
                    'resized' => "{$newWidth}x{$newHeight}",
                ]);
            }

            // Convert to WebP with quality 85 (good balance between quality and size)
            $success = imagewebp($image, $fullWebpPath, 85);

            // Free up memory
            imagedestroy($image);
            
            // Delete temporary file
            Storage::disk('public')->delete($tempPath);
            
            // Force garbage collection
            gc_collect_cycles();

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

        $query = Berita::query()->with(['category', 'creator', 'updater']);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('teaser', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        // Determine sort field and direction with safe defaults
        $sortBy = $request->query('sort_by', 'updated_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // Whitelist allowed sort columns
        $allowedSorts = ['title', 'updated_at', 'created_at', 'published', 'popular'];
        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'updated_at';
        }

        // Handle per-page pagination with whitelist
        $perPage = (int) $request->query('per_page', 10);
        $allowedPerPage = [5, 10, 20, 50];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $beritas = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();


        // Transform for lightweight payload
        $data = [
            'data' => $beritas->getCollection()->transform(function ($b) {
                return [
                    'id' => $b->id,
                    'title' => $b->title,
                    'slug' => $b->slug,
                    'published' => (int) $b->published,
                    'popular' => (int) $b->popular,
                    'category_id' => $b->category_id,
                    'category' => $b->category ? [
                        'id' => $b->category->id,
                        'name' => $b->category->category_name,
                    ] : null,
                    'image' => $b->image_url,
                    'created_by' => $b->creator ? [
                        'id' => $b->creator->id,
                        'name' => $b->creator->name,
                    ] : null,
                    'updated_by' => $b->updater ? [
                        'id' => $b->updater->id,
                        'name' => $b->updater->name,
                    ] : null,
                    'updated_at' => $b->updated_at?->toDateTimeString(),
                ];
            }),
            'links' => [
                'first' => $beritas->url(1),
                'last' => $beritas->url($beritas->lastPage()),
                'prev' => $beritas->previousPageUrl(),
                'next' => $beritas->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $beritas->currentPage(),
                'last_page' => $beritas->lastPage(),
                'per_page' => $beritas->perPage(),
                'total' => $beritas->total(),
            ],
        ];

        return Inertia::render('Beritas/Index', [
            'beritas' => $data,
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
        $categories = BeritaCategory::where('is_active', 1)->get(['id', 'category_name']);
        return Inertia::render('Beritas/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Creating new berita', [
                'user_id' => auth()->id(),
                'title' => $request->input('title'),
                'published_at_input' => $request->input('published_at'),
            ]);

            $rules = [
                'title' => ['required', 'array'],
                'title.id' => ['required', 'string', 'max:255'],
                'title.en' => ['nullable', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', 'unique:beritas'],
                'teaser' => ['nullable', 'array'],
                'teaser.id' => ['nullable', 'string'],
                'teaser.en' => ['nullable', 'string'],
                'content' => ['required', 'array'],
                'content.id' => ['required', 'string'],
                'content.en' => ['nullable', 'string'],
                'published' => ['nullable', 'integer'],
                'published_at' => ['nullable', 'date'],
                'category_id' => ['nullable', 'integer', 'exists:berita_categories,id'],
                'meta_title' => ['nullable', 'array'],
                'meta_title.id' => ['nullable', 'string', 'max:255'],
                'meta_title.en' => ['nullable', 'string', 'max:255'],
                'meta_keyword' => ['nullable', 'array'],
                'meta_keyword.id' => ['nullable', 'string', 'max:255'],
                'meta_keyword.en' => ['nullable', 'string', 'max:255'],
                'meta_content' => ['nullable', 'array'],
                'meta_content.id' => ['nullable', 'string'],
                'meta_content.en' => ['nullable', 'string'],
                'popular' => ['nullable', 'integer'],
            ];

            if ($request->hasFile('image')) {
                $rules['image'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'];
                Log::debug('Processing uploaded image file', [
                    'original_name' => $request->file('image')->getClientOriginalName(),
                    'size' => $request->file('image')->getSize(),
                    'mime' => $request->file('image')->getMimeType(),
                ]);
            } else {
                $rules['image'] = ['nullable', 'string'];
            }

            $messages = [
                // Title
                'title.required' => 'Judul berita wajib diisi.',
                'title.array' => 'Format judul tidak valid.',
                'title.id.required' => 'Judul dalam Bahasa Indonesia wajib diisi.',
                'title.id.string' => 'Judul harus berupa teks.',
                'title.id.max' => 'Judul tidak boleh lebih dari 255 karakter.',
                'title.en.string' => 'Judul dalam Bahasa Inggris harus berupa teks.',
                'title.en.max' => 'Judul dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
                
                // Slug
                'slug.string' => 'Slug harus berupa teks.',
                'slug.max' => 'Slug tidak boleh lebih dari 255 karakter.',
                'slug.unique' => 'Slug sudah digunakan. Silakan gunakan slug yang berbeda.',
                
                // Teaser
                'teaser.array' => 'Format teaser tidak valid.',
                'teaser.id.string' => 'Teaser harus berupa teks.',
                'teaser.en.string' => 'Teaser dalam Bahasa Inggris harus berupa teks.',
                
                // Content
                'content.required' => 'Konten berita wajib diisi.',
                'content.array' => 'Format konten tidak valid.',
                'content.id.required' => 'Konten dalam Bahasa Indonesia wajib diisi.',
                'content.id.string' => 'Konten harus berupa teks.',
                'content.en.string' => 'Konten dalam Bahasa Inggris harus berupa teks.',
                
                // Image
                'image.required' => 'Gambar berita wajib diunggah.',
                'image.image' => 'File yang diunggah harus berupa gambar.',
                'image.mimes' => 'Gambar harus berformat JPEG, PNG, JPG, atau WebP.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
                'image.string' => 'URL gambar harus berupa teks yang valid.',
                
                // Published
                'published.integer' => 'Status publikasi harus berupa angka.',
                'published_at.date' => 'Tanggal publikasi harus berupa tanggal yang valid.',
                
                // Category
                'category_id.integer' => 'ID kategori harus berupa angka.',
                'category_id.exists' => 'Kategori yang dipilih tidak valid atau tidak ditemukan.',
                
                // Meta Title
                'meta_title.array' => 'Format meta title tidak valid.',
                'meta_title.id.string' => 'Meta title harus berupa teks.',
                'meta_title.id.max' => 'Meta title tidak boleh lebih dari 255 karakter.',
                'meta_title.en.string' => 'Meta title dalam Bahasa Inggris harus berupa teks.',
                'meta_title.en.max' => 'Meta title dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
                
                // Meta Keyword
                'meta_keyword.array' => 'Format meta keyword tidak valid.',
                'meta_keyword.id.string' => 'Meta keyword harus berupa teks.',
                'meta_keyword.id.max' => 'Meta keyword tidak boleh lebih dari 255 karakter.',
                'meta_keyword.en.string' => 'Meta keyword dalam Bahasa Inggris harus berupa teks.',
                'meta_keyword.en.max' => 'Meta keyword dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
                
                // Meta Content
                'meta_content.array' => 'Format meta content tidak valid.',
                'meta_content.id.string' => 'Meta content harus berupa teks.',
                'meta_content.en.string' => 'Meta content dalam Bahasa Inggris harus berupa teks.',
                
                // Popular
                'popular.integer' => 'Status populer harus berupa angka.',
            ];

            $validated = $request->validate($rules, $messages);

            // Handle slug generation
            $inputSlug = trim($request->input('slug') ?? '');
            $sourceTitle = $request->input('title.id') ?? '';
            
            if (empty($inputSlug)) {
                
                // Auto-generate from title
                $slugBase = Str::slug($sourceTitle);
                $slug = $slugBase;
                $i = 1;
                
                // Ensure slug is unique
                while (Berita::where('slug', $slug)->exists()) {
                    $slug = $slugBase . '-' . $i;
                    $i++;
                }
                
                $validated['slug'] = $slug;
            } else {
                // User provided slug - just slugify it
                $slugified = Str::slug($inputSlug);
                
                Log::info('Checking slug uniqueness for create', [
                    'input_slug' => $inputSlug,
                    'slugified' => $slugified,
                ]);
                
                // Check if slugified version already exists
                if (Berita::where('slug', $slugified)->exists()) {
                    Log::warning('Duplicate slug detected, adding random suffix', [
                        'original_slug' => $slugified,
                    ]);
                    
                    // Add random string to make it unique
                    $randomSuffix = strtolower(Str::random(5));
                    $slug = $slugified . '-' . $randomSuffix;
                    
                    // Double check it's truly unique
                    while (Berita::where('slug', $slug)->exists()) {
                        $randomSuffix = strtolower(Str::random(5));
                        $slug = $slugified . '-' . $randomSuffix;
                    }
                    
                    Log::info('Slug modified with random suffix', [
                        'original_slug' => $slugified,
                        'final_slug' => $slug,
                    ]);
                    
                    $validated['slug'] = $slug;
                } else {
                    $validated['slug'] = $slugified;
                }
            }

            // Handle image conversion to WebP
            if ($request->hasFile('image')) {
                $validated['image'] = $this->convertToWebP($request->file('image'));
                Log::info('Image file stored successfully', ['path' => $validated['image']]);
            }

            // Normalize arrays if strings accidentally supplied
            if (is_string($request->input('title'))) {
                $validated['title'] = ['id' => $request->input('title')];
            }
            if (is_string($request->input('content'))) {
                $validated['content'] = ['id' => $request->input('content')];
            }
            foreach (['teaser','meta_title','meta_keyword','meta_content'] as $optField) {
                if (is_string($request->input($optField))) {
                    $validated[$optField] = ['id' => $request->input($optField)];
                }
            }

            // dd($validated, "Checkpoint A");

            
            $validated['published'] = !empty($validated['published']) ? 1 : 0;
            $validated['popular'] = !empty($validated['popular']) ? 1 : 0;
            
            // Handle published_at: if published but no date set, publish immediately
            if ($validated['published'] && empty($validated['published_at'])) {
                $validated['published_at'] = now();
            }
            
            $validated['created_by'] = auth()->id();

            $berita = Berita::create($validated);

            Log::info('Berita created successfully', [
                'berita_id' => $berita->id,
                'title' => $berita->title,
                'published' => $berita->published,
                'published_at' => $berita->published_at,
                'slug' => $berita->slug,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'Created berita: ' . $berita->title,
            ]);

            return redirect()->route('beritas.index')->with('status', 'Berita berhasil dibuat.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Error creating berita', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while creating the berita: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Berita $berita)
    {
        $berita->load(['creator', 'updater']);
        
        return Inertia::render('Beritas/Show', [
            'berita' => [
                'id' => $berita->id,
                'title' => $berita->title,
                'title_translations' => $berita->getTranslations('title'),
                'slug' => $berita->slug,
                'teaser' => $berita->teaser,
                'teaser_translations' => $berita->getTranslations('teaser'),
                'content' => $berita->content,
                'content_translations' => $berita->getTranslations('content'),
                'image' => $berita->image_url,
                'published' => (int) $berita->published,
                'published_at' => $berita->published_at?->format('Y-m-d\TH:i'),
                'category_id' => $berita->category_id,
                'meta_title' => $berita->meta_title,
                'meta_title_translations' => $berita->getTranslations('meta_title'),
                'meta_keyword' => $berita->meta_keyword,
                'meta_keyword_translations' => $berita->getTranslations('meta_keyword'),
                'meta_content' => $berita->meta_content,
                'meta_content_translations' => $berita->getTranslations('meta_content'),
                'popular' => (int) $berita->popular,
                'created_by' => $berita->creator ? [
                    'id' => $berita->creator->id,
                    'name' => $berita->creator->name,
                ] : null,
                'updated_by' => $berita->updater ? [
                    'id' => $berita->updater->id,
                    'name' => $berita->updater->name,
                ] : null,
            ],
        ]);
    }

    /**
     * Display a listing of the resource for public view.
     */
    public function indexPublic(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');

        $query = Berita::published()->with('category');
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('teaser', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('category_name', 'like', '%' . $category . '%');
            });
        }

        $beritas = $query->latest()->paginate(12)->withQueryString();

        // Get categories for sidebar
        $berita_categories = BeritaCategory::where('is_active', 1)
            ->latest()
            ->take(10)
            ->get();

        // Get random beritas for sidebar
        $random_beritas = Berita::published()
            ->inRandomOrder()
            ->take(5)
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'title' => $b->title,
                    'slug' => $b->slug,
                    'created_at' => $b->created_at?->format('Y-m-d H:i:s'),
                ];
            });

        // Transform beritas for frontend
        $data = [
            'data' => $beritas->getCollection()->transform(function ($b) {
                return [
                    'id' => $b->id,
                    'title' => $b->title,
                    'slug' => $b->slug,
                    'teaser' => $b->teaser,
                    'image' => $b->image_url,
                    'category' => $b->category ? [
                        'id' => $b->category->id,
                        'name' => $b->category->category_name,
                    ] : null,
                    'created_at' => $b->created_at?->format('Y-m-d H:i:s'),
                ];
            }),
            'links' => [
                'first' => $beritas->url(1),
                'last' => $beritas->url($beritas->lastPage()),
                'prev' => $beritas->previousPageUrl(),
                'next' => $beritas->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $beritas->currentPage(),
                'last_page' => $beritas->lastPage(),
                'per_page' => $beritas->perPage(),
                'total' => $beritas->total(),
            ],
        ];

        return Inertia::render('Beritas/IndexPublic', [
            'beritas' => $data,
            'berita_categories' => $berita_categories,
            'random_beritas' => $random_beritas,
            'filters' => [
                'search' => $search,
                'category' => $category,
            ],
        ]);
    }

    public function showPublic(Berita $berita)
    {

        $beritas = Berita::published()
            ->where('id', '!=', $berita->id)
            ->latest()
            ->take(4)
            ->get();

        // Transform related beritas to include translation maps for frontend locale switching
        $relatedBeritas = $beritas->map(function ($b) {
            return [
                'id' => $b->id,
                'slug' => $b->slug,
                'title' => $b->title,
                'title_translations' => $b->getTranslations('title'),
                'teaser' => $b->teaser,
                'teaser_translations' => $b->getTranslations('teaser'),
                'image' => $b->image_url,
                'created_at' => $b->created_at?->toDateTimeString(),
            ];
        });

        $berita_category = BeritaCategory::where('is_active', 1)
            ->latest()
            ->take(6)
            ->get();

        return Inertia::render('Beritas/ShowPublic', [
            'berita' => [
                'id' => $berita->id,
                'title' => $berita->title,
                'title_translations' => $berita->getTranslations('title'),
                'slug' => $berita->slug,
                'teaser' => $berita->teaser,
                'teaser_translations' => $berita->getTranslations('teaser'),
                'content' => $berita->content,
                'content_translations' => $berita->getTranslations('content'),
                'image' => $berita->image_url,
                'published' => (int) $berita->published,
                'category_id' => $berita->category_id,
                'meta_title' => $berita->meta_title,
                'meta_title_translations' => $berita->getTranslations('meta_title'),
                'meta_keyword' => $berita->meta_keyword,
                'meta_keyword_translations' => $berita->getTranslations('meta_keyword'),
                'meta_content' => $berita->meta_content,
                'meta_content_translations' => $berita->getTranslations('meta_content'),
                'popular' => (int) $berita->popular,
                'created_at' => $berita->created_at?->toDateTimeString(),
                'beritas' => $relatedBeritas,
            ],
            'berita_categories' => $berita_category,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Berita $berita)
    {
        $berita->load(['creator', 'updater']);
        $categories = BeritaCategory::where('is_active', 1)->get(['id', 'category_name']);
        
        return Inertia::render('Beritas/Edit', [
            'berita' => [
                'id' => $berita->id,
                'title' => $berita->title,
                'title_translations' => $berita->getTranslations('title'),
                'slug' => $berita->slug,
                'teaser' => $berita->teaser,
                'teaser_translations' => $berita->getTranslations('teaser'),
                'content' => $berita->content,
                'content_translations' => $berita->getTranslations('content'),
                'image' => $berita->image_url,
                'published' => (int) $berita->published,
                'published_at' => $berita->published_at?->format('Y-m-d\TH:i'),
                'category_id' => $berita->category_id,
                'meta_title' => $berita->meta_title,
                'meta_title_translations' => $berita->getTranslations('meta_title'),
                'meta_keyword' => $berita->meta_keyword,
                'meta_keyword_translations' => $berita->getTranslations('meta_keyword'),
                'meta_content' => $berita->meta_content,
                'meta_content_translations' => $berita->getTranslations('meta_content'),
                'popular' => (int) $berita->popular,
                'created_by' => $berita->creator ? [
                    'id' => $berita->creator->id,
                    'name' => $berita->creator->name,
                ] : null,
                'updated_by' => $berita->updater ? [
                    'id' => $berita->updater->id,
                    'name' => $berita->updater->name,
                ] : null,
            ],
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Berita $berita)
    {
        try {
            Log::info('Updating berita', [
                'user_id' => auth()->id(),
                'berita_id' => $berita->id,
                'title' => $request->input('title'),
                'published_at_input' => $request->input('published_at'),
            ]);

            $rules = [
                'title' => ['required', 'array'],
                'title.id' => ['required', 'string', 'max:255'],
                'title.en' => ['nullable', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255'],
                'teaser' => ['nullable', 'array'],
                'teaser.id' => ['nullable', 'string'],
                'teaser.en' => ['nullable', 'string'],
                'content' => ['required', 'array'],
                'content.id' => ['required', 'string'],
                'content.en' => ['nullable', 'string'],
                'published' => ['nullable', 'integer'],
                'published_at' => ['nullable', 'date'],
                'category_id' => ['nullable', 'integer', 'exists:berita_categories,id'],
                'meta_title' => ['nullable', 'array'],
                'meta_title.id' => ['nullable', 'string', 'max:255'],
                'meta_title.en' => ['nullable', 'string', 'max:255'],
                'meta_keyword' => ['nullable', 'array'],
                'meta_keyword.id' => ['nullable', 'string', 'max:255'],
                'meta_keyword.en' => ['nullable', 'string', 'max:255'],
                'meta_content' => ['nullable', 'array'],
                'meta_content.id' => ['nullable', 'string'],
                'meta_content.en' => ['nullable', 'string'],
                'popular' => ['nullable', 'integer'],
            ];

            if ($request->hasFile('image')) {
                $rules['image'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'];
                Log::debug('Processing uploaded image file for update', [
                    'original_name' => $request->file('image')->getClientOriginalName(),
                    'size' => $request->file('image')->getSize(),
                    'mime' => $request->file('image')->getMimeType(),
                ]);
            } else {
                $rules['image'] = ['nullable', 'string'];
            }

            $messages = [
                // Title
                'title.required' => 'Judul berita wajib diisi.',
                'title.array' => 'Format judul tidak valid.',
                'title.id.required' => 'Judul dalam Bahasa Indonesia wajib diisi.',
                'title.id.string' => 'Judul harus berupa teks.',
                'title.id.max' => 'Judul tidak boleh lebih dari 255 karakter.',
                'title.en.string' => 'Judul dalam Bahasa Inggris harus berupa teks.',
                'title.en.max' => 'Judul dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
                
                // Slug
                'slug.string' => 'Slug harus berupa teks.',
                'slug.max' => 'Slug tidak boleh lebih dari 255 karakter.',
                
                // Teaser
                'teaser.array' => 'Format teaser tidak valid.',
                'teaser.id.string' => 'Teaser harus berupa teks.',
                'teaser.en.string' => 'Teaser dalam Bahasa Inggris harus berupa teks.',
                
                // Content
                'content.required' => 'Konten berita wajib diisi.',
                'content.array' => 'Format konten tidak valid.',
                'content.id.required' => 'Konten dalam Bahasa Indonesia wajib diisi.',
                'content.id.string' => 'Konten harus berupa teks.',
                'content.en.string' => 'Konten dalam Bahasa Inggris harus berupa teks.',
                
                // Image
                'image.required' => 'Gambar berita wajib diunggah.',
                'image.image' => 'File yang diunggah harus berupa gambar.',
                'image.mimes' => 'Gambar harus berformat JPEG, PNG, JPG, atau WebP.',
                'image.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
                'image.string' => 'URL gambar harus berupa teks yang valid.',
                
                // Published
                'published.integer' => 'Status publikasi harus berupa angka.',
                'published_at.date' => 'Tanggal publikasi harus berupa tanggal yang valid.',
                
                // Category
                'category_id.integer' => 'ID kategori harus berupa angka.',
                'category_id.exists' => 'Kategori yang dipilih tidak valid atau tidak ditemukan.',
                
                // Meta Title
                'meta_title.array' => 'Format meta title tidak valid.',
                'meta_title.id.string' => 'Meta title harus berupa teks.',
                'meta_title.id.max' => 'Meta title tidak boleh lebih dari 255 karakter.',
                'meta_title.en.string' => 'Meta title dalam Bahasa Inggris harus berupa teks.',
                'meta_title.en.max' => 'Meta title dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
                
                // Meta Keyword
                'meta_keyword.array' => 'Format meta keyword tidak valid.',
                'meta_keyword.id.string' => 'Meta keyword harus berupa teks.',
                'meta_keyword.id.max' => 'Meta keyword tidak boleh lebih dari 255 karakter.',
                'meta_keyword.en.string' => 'Meta keyword dalam Bahasa Inggris harus berupa teks.',
                'meta_keyword.en.max' => 'Meta keyword dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
                
                // Meta Content
                'meta_content.array' => 'Format meta content tidak valid.',
                'meta_content.id.string' => 'Meta content harus berupa teks.',
                'meta_content.en.string' => 'Meta content dalam Bahasa Inggris harus berupa teks.',
                
                // Popular
                'popular.integer' => 'Status populer harus berupa angka.',
            ];

            $validated = $request->validate($rules, $messages);

            // Handle image conversion to WebP
            if ($request->hasFile('image')) {
                $validated['image'] = $this->convertToWebP($request->file('image'));
                Log::info('Image file updated successfully', ['path' => $validated['image']]);
            }

            // Normalize arrays if strings accidentally supplied
            if (is_string($request->input('title'))) {
                $validated['title'] = ['id' => $request->input('title')];
            }
            if (is_string($request->input('content'))) {
                $validated['content'] = ['id' => $request->input('content')];
            }
            foreach (['teaser','meta_title','meta_keyword','meta_content'] as $optField) {
                if (is_string($request->input($optField))) {
                    $validated[$optField] = ['id' => $request->input($optField)];
                }
            }

            // Handle slug generation
            $inputSlug = trim($request->input('slug') ?? '');
            $sourceTitle = $request->input('title.id') ?? '';
            
            if (empty($inputSlug)) {
                // Auto-generate from title
                $slugBase = Str::slug($sourceTitle);
                $slug = $slugBase;
                $i = 1;
                
                // Ensure slug is unique (exclude current berita)
                while (Berita::where('slug', $slug)->where('id', '!=', $berita->id)->exists()) {
                    $slug = $slugBase . '-' . $i;
                    $i++;
                }
                
                $validated['slug'] = $slug;
            } else {
                // User provided slug - just slugify it
                $slugified = Str::slug($inputSlug);
                
                Log::info('Checking slug uniqueness for update', [
                    'berita_id' => $berita->id,
                    'input_slug' => $inputSlug,
                    'slugified' => $slugified,
                ]);
                
                // Check if slugified version already exists (excluding current berita)
                if (Berita::where('slug', $slugified)->where('id', '!=', $berita->id)->exists()) {
                    Log::warning('Duplicate slug detected for update, adding random suffix', [
                        'berita_id' => $berita->id,
                        'original_slug' => $slugified,
                    ]);
                    
                    // Add random string to make it unique
                    $randomSuffix = strtolower(Str::random(5));
                    $slug = $slugified . '-' . $randomSuffix;
                    
                    // Double check it's truly unique (excluding current berita)
                    while (Berita::where('slug', $slug)->where('id', '!=', $berita->id)->exists()) {
                        $randomSuffix = strtolower(Str::random(5));
                        $slug = $slugified . '-' . $randomSuffix;
                    }
                    
                    Log::info('Slug modified with random suffix for update', [
                        'berita_id' => $berita->id,
                        'original_slug' => $slugified,
                        'final_slug' => $slug,
                    ]);
                    
                    $validated['slug'] = $slug;
                } else {
                    $validated['slug'] = $slugified;
                }
            }
            
            $validated['published'] = !empty($validated['published']) ? 1 : 0;
            $validated['popular'] = !empty($validated['popular']) ? 1 : 0;
            
            // Handle published_at: if published but no date set, publish immediately
            if ($validated['published'] && empty($validated['published_at'])) {
                $validated['published_at'] = now();
            }
            
            $validated['updated_by'] = auth()->id();

            $berita->update($validated);

            Log::info('Berita updated successfully', [
                'berita_id' => $berita->id,
                'title' => $berita->title,
                'published' => $berita->published,
                'published_at' => $berita->published_at,
                'slug' => $berita->slug,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'Updated berita: ' . $berita->title,
            ]);

            return redirect()->route('beritas.index')->with('status', 'Berita berhasil diperbarui.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Error updating berita', [
                'berita_id' => $berita->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while updating the berita: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Berita $berita)
    {
        $beritaTitle = $berita->title;
        $berita->delete();
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Deleted berita: ' . $beritaTitle,
        ]);
        
        return redirect()->route('beritas.index')->with('status', 'Berita deleted.');
    }
}
