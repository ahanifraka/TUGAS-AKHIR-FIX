<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\AlbumImage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AlbumImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Album $album)
    {
        $search = $request->query('search');

        $query = $album->images()->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Handle per-page with whitelist
        $perPage = (int) $request->query('per_page', 10);
        $allowedPerPage = [5, 10, 20, 50];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $images = $query->paginate($perPage)->withQueryString();

        $data = [
            'data' => $images->getCollection()->transform(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => $image->image,
                    'title' => $image->title,
                    'description' => $image->description,
                    'published' => (bool) $image->published,
                    'updated_at' => $image->updated_at?->toDateTimeString(),
                ];
            }),
            'links' => [
                'first' => $images->url(1),
                'last' => $images->url($images->lastPage()),
                'prev' => $images->previousPageUrl(),
                'next' => $images->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $images->currentPage(),
                'last_page' => $images->lastPage(),
                'per_page' => $images->perPage(),
                'total' => $images->total(),
            ],
        ];

        return Inertia::render('Albums/Images/Index', [
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
            ],
            'images' => $data,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Album $album)
    {
        return Inertia::render('Albums/Images/Create', [
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Album $album)
    {
        $messages = [
            // Images Array
            'images.required' => 'Minimal satu gambar harus diunggah.',
            'images.array' => 'Format data gambar tidak valid.',
            'images.min' => 'Minimal satu gambar harus diunggah.',
            
            // Image File
            'images.*.image.required' => 'File gambar wajib diunggah.',
            'images.*.image.image' => 'File yang diunggah harus berupa gambar.',
            'images.*.image.mimes' => 'Gambar harus berformat JPEG, PNG, JPG, GIF, atau WebP.',
            'images.*.image.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
            
            // Title
            'images.*.title.array' => 'Format judul tidak valid.',
            'images.*.title.id.string' => 'Judul harus berupa teks.',
            'images.*.title.id.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            'images.*.title.en.string' => 'Judul dalam Bahasa Inggris harus berupa teks.',
            'images.*.title.en.max' => 'Judul dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
            
            // Description
            'images.*.description.array' => 'Format deskripsi tidak valid.',
            'images.*.description.id.string' => 'Deskripsi harus berupa teks.',
            'images.*.description.en.string' => 'Deskripsi dalam Bahasa Inggris harus berupa teks.',
            
            // Published
            'images.*.published.boolean' => 'Status publikasi harus berupa ya atau tidak.',
        ];

        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*.image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'images.*.title' => ['nullable', 'array'],
            'images.*.title.id' => ['nullable', 'string', 'max:255'],
            'images.*.title.en' => ['nullable', 'string', 'max:255'],
            'images.*.description' => ['nullable', 'array'],
            'images.*.description.id' => ['nullable', 'string'],
            'images.*.description.en' => ['nullable', 'string'],
            'images.*.published' => ['nullable', 'boolean'],
        ], $messages);

        try {
            foreach ($validated['images'] as $imageData) {
                // Normalize translations
                $title = $imageData['title'] ?? [];
                if (is_string($title)) $title = ['id' => $title];
                
                $description = $imageData['description'] ?? [];
                if (is_string($description)) $description = ['id' => $description];

                $album->images()->create([
                    'image' => $imageData['image'], // This will be converted to WebP by the model mutator
                    'title' => $title,
                    'description' => $description,
                    'published' => (bool)($imageData['published'] ?? true),
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error storing album images: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to save images: ' . $e->getMessage()]);
        }

        return redirect()->route('albums.show', $album)->with('status', 'Images added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Album $album, AlbumImage $image)
    {
        if ($image->album_id !== $album->id) {
            abort(404);
        }

        return Inertia::render('Albums/Images/Show', [
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
            ],
            'image' => [
                'id' => $image->id,
                'image' => $image->image,
                'title' => $image->title,
                'description' => $image->description,
                'published' => (bool) $image->published,
                'created_at' => $image->created_at?->toDateTimeString(),
                'updated_at' => $image->updated_at?->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Album $album, AlbumImage $image)
    {
        if ($image->album_id !== $album->id) {
            abort(404);
        }

        return Inertia::render('Albums/Images/Edit', [
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
            ],
            'image' => [
                'id' => $image->id,
                // gunakan URL ter-resolve agar preview tidak rusak di nested route
                'image' => $image->image_url,
                'title' => $image->title,
                'title_translations' => $image->getTranslations('title'),
                'description' => $image->description,
                'description_translations' => $image->getTranslations('description'),
                'published' => (bool) $image->published,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Album $album, AlbumImage $image)
    {
        if ($image->album_id !== $album->id) {
            abort(404);
        }

        $messages = [
            // Image
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat JPEG, PNG, JPG, GIF, atau WebP.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 5MB.',
            
            // Title
            'title.array' => 'Format judul tidak valid.',
            'title.id.string' => 'Judul harus berupa teks.',
            'title.id.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            'title.en.string' => 'Judul dalam Bahasa Inggris harus berupa teks.',
            'title.en.max' => 'Judul dalam Bahasa Inggris tidak boleh lebih dari 255 karakter.',
            
            // Description
            'description.array' => 'Format deskripsi tidak valid.',
            'description.id.string' => 'Deskripsi harus berupa teks.',
            'description.en.string' => 'Deskripsi dalam Bahasa Inggris harus berupa teks.',
            
            // Published
            'published.boolean' => 'Status publikasi harus berupa ya atau tidak.',
        ];

        $validated = $request->validate([
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'title' => ['nullable', 'array'],
            'title.id' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.id' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'published' => ['nullable', 'boolean'],
        ], $messages);

        // Default ke nilai sebelumnya bila tidak dikirim
        $validated['published'] = (bool)($validated['published'] ?? $image->published);

        // Normalize translations if strings sent
        if (isset($validated['title']) && is_string($validated['title'])) {
            $validated['title'] = ['id' => $validated['title']];
        }
        if (isset($validated['description']) && is_string($validated['description'])) {
            $validated['description'] = ['id' => $validated['description']];
        }

        $image->update($validated);

        return redirect()->route('albums.show', $album)->with('status', 'Image updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Album $album, AlbumImage $image)
    {
        if ($image->album_id !== $album->id) {
            abort(404);
        }

        $image->delete();
        
        return redirect()->route('albums.show', $album)->with('status', 'Image deleted successfully.');
    }
}