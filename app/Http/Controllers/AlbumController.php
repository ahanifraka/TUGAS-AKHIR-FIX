<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\ActivityLog;
use Exception;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource for public.
     */
    public function indexPublic(Request $request)
    {
        $search = $request->query('search');

        $query = Album::where('published', true);
        if ($search) {
            $query->where(function ($q) use ($search) {
                // Titles/descriptions are JSON strings now; LIKE still works as substring match
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $albums = $query->latest()->orderBy('created_at')->paginate(12)->withQueryString();

        $data = [
            'data' => $albums->getCollection()->transform(function ($a) {
                $locale = app()->getLocale();
                $title = $a->getTranslation('title', $locale) ?: $a->getTranslation('title', 'id') ?: (is_array($a->title) ? ($a->title['id'] ?? null) : $a->title);
                $desc = $a->getTranslation('description', $locale) ?: $a->getTranslation('description', 'id') ?: (is_array($a->description) ? ($a->description['id'] ?? null) : $a->description);
                return [
                    'id' => $a->id,
                    'title' => $title,
                    'title_translations' => $a->getTranslations('title'),
                    'description' => $desc,
                    'description_translations' => $a->getTranslations('description'),
                    'image' => $a->image,
                    'created_at' => $a->created_at?->format('d M Y'),
                    'images_count' => $a->images()->where('published', true)->count(),
                ];
            }),
            'links' => [
                'first' => $albums->url(1),
                'last' => $albums->url($albums->lastPage()),
                'prev' => $albums->previousPageUrl(),
                'next' => $albums->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $albums->currentPage(),
                'last_page' => $albums->lastPage(),
                'per_page' => $albums->perPage(),
                'total' => $albums->total(),
            ],
        ];

        return Inertia::render('Albums/IndexPublic', [
            'albums' => $data,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Display the specified resource for public.
     */
    public function showPublic(Album $album)
    {
        if (!$album->published) {
            abort(404);
        }

        return Inertia::render('Albums/ShowPublic', [
            'album' => [
                'id' => $album->id,
                'title' => $album->getTranslation('title', app()->getLocale()) ?: $album->getTranslation('title', 'id') ?: (is_array($album->title) ? ($album->title['id'] ?? null) : $album->title),
                'title_translations' => $album->getTranslations('title'),
                'description' => $album->getTranslation('description', app()->getLocale()) ?: $album->getTranslation('description', 'id') ?: (is_array($album->description) ? ($album->description['id'] ?? null) : $album->description),
                'description_translations' => $album->getTranslations('description'),
                // Use resolved URL to avoid broken relative paths
                'image' => $album->image_url,
                'created_at' => $album->created_at?->format('d M Y'),
            ],
            // Map images to include resolved URLs
            'images' => $album->images()
                ->where('published', true)
                ->latest()
                ->get()
                ->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'image' => $img->image_url,
                        'title' => $img->title,
                        'description' => $img->description,
                    ];
                }),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Album::withCount('images');
        if ($search) {
            $query->where(function ($q) use ($search) {
                                $q->where('title', 'like', '%' . $search . '%')
                                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Handle per-page pagination with whitelist
        $perPage = (int) $request->query('per_page', 10);
        $allowedPerPage = [5, 10, 20, 50];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $albums = $query->latest()->orderBy('created_at')->paginate($perPage)->withQueryString();

        $data = [
            'data' => $albums->getCollection()->transform(function ($a) {
                $locale = app()->getLocale();
                $title = $a->getTranslation('title', $locale) ?: $a->getTranslation('title', 'id') ?: (is_array($a->title) ? ($a->title['id'] ?? null) : $a->title);
                return [
                    'id' => $a->id,
                    'title' => $title,
                    'title_translations' => $a->getTranslations('title'),
                    'image' => $a->image,
                    'published' => (bool) $a->published,
                    'images_count' => $a->images_count,
                    'updated_at' => $a->updated_at?->toDateTimeString(),
                ];
            }),
            'links' => [
                'first' => $albums->url(1),
                'last' => $albums->url($albums->lastPage()),
                'prev' => $albums->previousPageUrl(),
                'next' => $albums->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $albums->currentPage(),
                'last_page' => $albums->lastPage(),
                'per_page' => $albums->perPage(),
                'total' => $albums->total(),
            ],
        ];

        return Inertia::render('Albums/Index', [
            'albums' => $data,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Albums/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Creating new album', [
                'user_id' => Auth::id() ?? 'guest',
                'title' => $request->input('title'),
                'images_count' => is_array($request->input('images')) ? count($request->input('images')) : 0,
            ]);

            // Provide detailed debug info for nested images if present
            if (is_array($request->input('images'))) {
                $imagesPreview = array_slice($request->input('images'), 0, 3);
                Log::debug('Validating nested images array for album creation', [
                    'preview' => $imagesPreview,
                ]);
            }

            // Validate cover image and translatable fields
            $rules = [
                'title' => ['required'], // string or array
                'title.id' => ['sometimes', 'string', 'max:255'],
                'title.en' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable'], // string or array
                'description.id' => ['nullable', 'string'],
                'description.en' => ['nullable', 'string'],
                'published' => ['nullable', 'boolean'],
                'images' => ['nullable', 'array'],
                'images.*.title' => ['nullable', 'array'],
                'images.*.title.id' => ['nullable', 'string', 'max:255'],
                'images.*.title.en' => ['nullable', 'string', 'max:255'],
                'images.*.description' => ['nullable', 'array'],
                'images.*.description.id' => ['nullable', 'string'],
                'images.*.description.en' => ['nullable', 'string'],
                'images.*.published' => ['nullable', 'boolean'],
            ];

            // Cover image can be file or URL
            if ($request->hasFile('image')) {
                $rules['image'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'];
            } else {
                $rules['image'] = ['nullable', 'string'];
            }

            // Album images - check if any are files
            if ($request->has('images') && is_array($request->input('images'))) {
                foreach ($request->input('images') as $index => $imageData) {
                    if ($request->hasFile("images.{$index}.image")) {
                        $rules["images.{$index}.image"] = ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'];
                    } else {
                        $rules["images.{$index}.image"] = ['required'];
                    }
                    $rules["images.{$index}.title"] = ['nullable', 'array'];
                    $rules["images.{$index}.title.id"] = ['nullable', 'string', 'max:255'];
                    $rules["images.{$index}.title.en"] = ['nullable', 'string', 'max:255'];
                    $rules["images.{$index}.description"] = ['nullable', 'array'];
                    $rules["images.{$index}.description.id"] = ['nullable', 'string'];
                    $rules["images.{$index}.description.en"] = ['nullable', 'string'];
                    $rules["images.{$index}.published"] = ['nullable', 'boolean'];
                }
            }

            $messages = [
                // Title
                'title.required' => 'Judul album wajib diisi.',
                'title.id.string' => 'Judul harus berupa teks.',
                'title.id.max' => 'Judul tidak boleh lebih dari 255 karakter.',
                'title.en.string' => 'Judul dalam Bahasa Inggris harus berupa teks.',
                'title.en.max' => 'Judul dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
                
                // Description
                'description.id.string' => 'Deskripsi harus berupa teks.',
                'description.en.string' => 'Deskripsi dalam Bahasa Inggris harus berupa teks.',
                
                // Cover Image
                'image.image' => 'File cover album harus berupa gambar.',
                'image.mimes' => 'Cover album harus berformat JPEG, PNG, JPG, GIF, atau WebP.',
                'image.max' => 'Ukuran cover album tidak boleh lebih dari 5MB.',
                'image.string' => 'URL gambar harus berupa teks yang valid.',
                
                // Published
                'published.boolean' => 'Status publikasi harus berupa ya atau tidak.',
                
                // Images Array
                'images.array' => 'Format data gambar tidak valid.',
                'images.*.title.array' => 'Format judul gambar tidak valid.',
                'images.*.title.id.string' => 'Judul gambar harus berupa teks.',
                'images.*.title.id.max' => 'Judul gambar tidak boleh lebih dari 255 karakter.',
                'images.*.title.en.string' => 'Judul gambar dalam Bahasa Inggris harus berupa teks.',
                'images.*.title.en.max' => 'Judul gambar dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
                'images.*.description.array' => 'Format deskripsi gambar tidak valid.',
                'images.*.description.id.string' => 'Deskripsi gambar harus berupa teks.',
                'images.*.description.en.string' => 'Deskripsi gambar dalam Bahasa Inggris harus berupa teks.',
                'images.*.published.boolean' => 'Status publikasi gambar harus berupa ya atau tidak.',
                'images.*.image.required' => 'File gambar wajib diunggah.',
                'images.*.image.image' => 'File yang diunggah harus berupa gambar.',
                'images.*.image.mimes' => 'Gambar harus berformat JPEG, PNG, JPG, GIF, atau WebP.',
                'images.*.image.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
            ];

            $validated = $request->validate($rules, $messages);

            // Normalize translatable fields
            $incomingTitle = $request->input('title');
            $incomingDesc = $request->input('description');
            $titleData = is_array($incomingTitle) ? $incomingTitle : ['id' => (string) $incomingTitle];
            $descData = is_array($incomingDesc) ? $incomingDesc : (isset($incomingDesc) ? ['id' => (string) $incomingDesc] : null);

            $validated['published'] = (bool)($validated['published'] ?? true);


            $album = Album::create([
                'title' => $titleData,
                'description' => $descData,
                'image' => $validated['image'],
                'published' => $validated['published'],
                'created_by' => Auth::id(),
            ]);

            // Create album images if provided
            $createdImages = 0;
            if (isset($validated['images']) && is_array($validated['images'])) {
                foreach ($validated['images'] as $index => $imageData) {
                    Log::debug('Processing album image', [
                        'index' => $index,
                        'has_image' => isset($imageData['image']),
                        'is_file' => isset($imageData['image']) && $imageData['image'] instanceof \Illuminate\Http\UploadedFile,
                        'title' => $imageData['title'] ?? 'no title',
                    ]);
                    
                    // Normalize translations
                    $title = $imageData['title'] ?? [];
                    if (is_string($title)) $title = ['id' => $title];
                    
                    $description = $imageData['description'] ?? [];
                    if (is_string($description)) $description = ['id' => $description];

                    $album->images()->create([
                        'image' => $imageData['image'],
                        'title' => $title,
                        'description' => $description,
                        'published' => (bool)($imageData['published'] ?? true),
                    ]);
                    $createdImages++;
                }
            }

            Log::info('Album created successfully', [
                'album_id' => $album->id,
                'title' => $album->title,
                'published' => $album->published,
                'images_created' => $createdImages,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Created album: ' . $album->title,
            ]);

            return redirect()->route('albums.index')->with('status', 'Album created.');
        } catch (ValidationException $e) {
            Log::warning('Validation failed when creating album', [
                'errors' => $e->errors(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            Log::error('Error creating album', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while creating the album: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Album $album)
    {
        $album->load(['creator', 'updater']);

        return Inertia::render('Albums/Show', [
            'album' => [
                'id' => $album->id,
                'title' => $album->getTranslation('title', app()->getLocale()) ?: $album->getTranslation('title', 'id') ?: (is_array($album->title) ? ($album->title['id'] ?? null) : $album->title),
                'title_translations' => $album->getTranslations('title'),
                'description' => $album->getTranslation('description', app()->getLocale()) ?: $album->getTranslation('description', 'id') ?: (is_array($album->description) ? ($album->description['id'] ?? null) : $album->description),
                'description_translations' => $album->getTranslations('description'),
                // Use resolved URL to avoid broken relative paths
                'image' => $album->image_url,
                'published' => (bool) $album->published,
                'created_at' => $album->created_at?->toDateTimeString(),
                'updated_at' => $album->updated_at?->toDateTimeString(),
                'creator' => $album->creator ? [
                    'id' => $album->creator->id,
                    'name' => $album->creator->name,
                ] : null,
                'updater' => $album->updater ? [
                    'id' => $album->updater->id,
                    'name' => $album->updater->name,
                ] : null,
            ],
            // Map images to include resolved URLs
            'images' => $album->images()
                ->latest()
                ->get()
                ->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'image' => $img->image_url,
                        'title' => $img->title,
                        'description' => $img->description,
                        'published' => (bool) $img->published,
                    ];
                }),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Album $album)
    {
        return Inertia::render('Albums/Edit', [
            'album' => [
                'id' => $album->id,
                'title' => $album->getTranslation('title', app()->getLocale()) ?: $album->getTranslation('title', 'id') ?: (is_array($album->title) ? ($album->title['id'] ?? null) : $album->title),
                'title_translations' => $album->getTranslations('title'),
                'description' => $album->getTranslation('description', app()->getLocale()) ?: $album->getTranslation('description', 'id') ?: (is_array($album->description) ? ($album->description['id'] ?? null) : $album->description),
                'description_translations' => $album->getTranslations('description'),
                // Use resolved URL for edit preview consistency
                'image' => $album->image_url,
                'published' => (bool) $album->published,
            ],
            // Provide existing images with resolved URLs for edit/delete UI
            'images' => $album->images()
                ->latest()
                ->get()
                ->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'image' => $img->image_url,
                        'title' => $img->title,
                        'description' => $img->description,
                        'published' => (bool) $img->published,
                    ];
                }),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Album $album)
    {
        // Build validation rules
        $rules = [
            'title' => ['required'], // string or array
            'title.id' => ['sometimes', 'string', 'max:255'],
            'title.en' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable'], // string or array
            'description.id' => ['sometimes', 'string'],
            'description.en' => ['sometimes', 'string'],
            'published' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*.title' => ['nullable', 'array'],
            'images.*.title.id' => ['nullable', 'string', 'max:255'],
            'images.*.title.en' => ['nullable', 'string', 'max:255'],
            'images.*.description' => ['nullable', 'array'],
            'images.*.description.id' => ['nullable', 'string'],
            'images.*.description.en' => ['nullable', 'string'],
            'images.*.published' => ['nullable', 'boolean'],
        ];

        // Cover image can be file or URL
        if ($request->hasFile('image')) {
            $rules['image'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'];
        } else {
            $rules['image'] = ['nullable', 'string'];
        }

        // Album images - check if any are files
        if ($request->has('images') && is_array($request->input('images'))) {
            foreach ($request->input('images') as $index => $imageData) {
                if ($request->hasFile("images.{$index}.image")) {
                    $rules["images.{$index}.image"] = ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'];
                } else {
                    $rules["images.{$index}.image"] = ['required'];
                }
                $rules["images.{$index}.title"] = ['nullable', 'array'];
                $rules["images.{$index}.title.id"] = ['nullable', 'string', 'max:255'];
                $rules["images.{$index}.title.en"] = ['nullable', 'string', 'max:255'];
                $rules["images.{$index}.description"] = ['nullable', 'array'];
                $rules["images.{$index}.description.id"] = ['nullable', 'string'];
                $rules["images.{$index}.description.en"] = ['nullable', 'string'];
                $rules["images.{$index}.published"] = ['nullable', 'boolean'];
            }
        }

        $messages = [
            // Title
            'title.required' => 'Judul album wajib diisi.',
            'title.id.string' => 'Judul harus berupa teks.',
            'title.id.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            'title.en.string' => 'Judul dalam Bahasa Inggris harus berupa teks.',
            'title.en.max' => 'Judul dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
            
            // Description
            'description.id.string' => 'Deskripsi harus berupa teks.',
            'description.en.string' => 'Deskripsi dalam Bahasa Inggris harus berupa teks.',
            
            // Cover Image
            'image.image' => 'File cover album harus berupa gambar.',
            'image.mimes' => 'Cover album harus berformat JPEG, PNG, JPG, GIF, atau WebP.',
            'image.max' => 'Ukuran cover album tidak boleh lebih dari 5MB.',
            'image.string' => 'URL gambar harus berupa teks yang valid.',
            
            // Published
            'published.boolean' => 'Status publikasi harus berupa ya atau tidak.',
            
            // Images Array
            'images.array' => 'Format data gambar tidak valid.',
            'images.*.title.array' => 'Format judul gambar tidak valid.',
            'images.*.title.id.string' => 'Judul gambar harus berupa teks.',
            'images.*.title.id.max' => 'Judul gambar tidak boleh lebih dari 255 karakter.',
            'images.*.title.en.string' => 'Judul gambar dalam Bahasa Inggris harus berupa teks.',
            'images.*.title.en.max' => 'Judul gambar dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
            'images.*.description.array' => 'Format deskripsi gambar tidak valid.',
            'images.*.description.id.string' => 'Deskripsi gambar harus berupa teks.',
            'images.*.description.en.string' => 'Deskripsi gambar dalam Bahasa Inggris harus berupa teks.',
            'images.*.published.boolean' => 'Status publikasi gambar harus berupa ya atau tidak.',
            'images.*.image.required' => 'File gambar wajib diunggah.',
            'images.*.image.image' => 'File yang diunggah harus berupa gambar.',
            'images.*.image.mimes' => 'Gambar harus berformat JPEG, PNG, JPG, GIF, atau WebP.',
            'images.*.image.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
        ];

        $validated = $request->validate($rules, $messages);

        $validated['published'] = (bool)($validated['published'] ?? true);

        // Normalize translatable fields
        $incomingTitle = $request->input('title');
        $incomingDesc = $request->input('description');
        $titleData = is_array($incomingTitle) ? $incomingTitle : ['id' => (string) $incomingTitle];
        $descData = is_array($incomingDesc) ? $incomingDesc : (isset($incomingDesc) ? ['id' => (string) $incomingDesc] : null);

        // Update only album fields to avoid setting non-fillable attributes
        $album->update([
            'title' => $titleData,
            'description' => $descData,
            'image' => $validated['image'] ?? null,
            'published' => $validated['published'],
            'updated_by' => Auth::id(),
        ]);

        // Create album images if provided (supports individual image additions)
        if (isset($validated['images']) && is_array($validated['images'])) {
            foreach ($validated['images'] as $index => $imageData) {
                Log::debug('Processing album image for update', [
                    'album_id' => $album->id,
                    'index' => $index,
                    'has_image' => isset($imageData['image']),
                    'is_file' => isset($imageData['image']) && $imageData['image'] instanceof \Illuminate\Http\UploadedFile,
                    'title' => $imageData['title'] ?? 'no title',
                ]);
                
                // Normalize translations
                $title = $imageData['title'] ?? [];
                if (is_string($title)) $title = ['id' => $title];
                
                $description = $imageData['description'] ?? [];
                if (is_string($description)) $description = ['id' => $description];

                $album->images()->create([
                    'image' => $imageData['image'],
                    'title' => $title,
                    'description' => $description,
                    'published' => (bool)($imageData['published'] ?? true),
                ]);
            }
        }

        Log::info('Album updated successfully', [
            'album_id' => $album->id,
            'title' => $album->title,
            'updated_by' => Auth::id(),
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Updated album: ' . $album->title,
        ]);

        return redirect()->route('albums.index')->with('status', 'Album updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Album $album)
    {
        $albumTitle = $album->title;
        
        Log::info('Deleting album', [
            'album_id' => $album->id,
            'title' => $albumTitle,
            'deleted_by' => Auth::id(),
        ]);

        $album->delete();

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Deleted album: ' . $albumTitle,
        ]);

        return redirect()->route('albums.index')->with('status', 'Album deleted.');
    }
}