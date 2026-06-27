<?php

namespace App\Http\Controllers;

use App\Models\BeritaCategory;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class BeritaCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = BeritaCategory::query();
        if ($search) {
            $query->where('category_name', 'like', '%' . $search . '%');
        }

        $categories = $query->latest()->orderBy('created_at')->paginate(10)->withQueryString();

        $data = [
            'data' => $categories->getCollection()->transform(function ($c) {
                return [
                    'id' => $c->id,
                    'category_name' => $c->category_name,
                    'category_slug' => $c->category_slug,
                    'created_by' => $c->created_by,
                    'updated_by' => $c->updated_by,
                    'is_active' => (bool) $c->is_active,
                    'updated_at' => $c->updated_at?->toDateTimeString(),
                ];
            }),
            'links' => [
                'first' => $categories->url(1),
                'last' => $categories->url($categories->lastPage()),
                'prev' => $categories->previousPageUrl(),
                'next' => $categories->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ],
        ];

        return Inertia::render('BeritaCategories/Index', [
            'categories' => $data,
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
        return Inertia::render('BeritaCategories/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Creating new berita category', [
                'user_id' => Auth::id() ?? 'guest',
                'category_name' => $request->input('category_name'),
            ]);

            $validated = $request->validate([
                'category_name' => ['required', 'string', 'max:255', 'unique:berita_categories,category_name'],
                'category_slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:berita_categories,category_slug'],
                'is_active' => ['nullable', 'boolean'],
            ], [
                'category_name.required' => 'Nama kategori wajib diisi.',
                'category_name.string' => 'Nama kategori harus berupa teks.',
                'category_name.max' => 'Nama kategori maksimal 255 karakter.',
                'category_name.unique' => 'Nama kategori sudah digunakan.',
                'category_slug.required' => 'Slug kategori wajib diisi.',
                'category_slug.string' => 'Slug kategori harus berupa teks.',
                'category_slug.max' => 'Slug kategori maksimal 255 karakter.',
                'category_slug.regex' => 'Slug hanya boleh huruf kecil, angka, dan tanda minus (-).',
                'category_slug.unique' => 'Slug kategori sudah digunakan.',
                'is_active.boolean' => 'Status aktif harus bernilai benar/salah.',
            ]);
    
            $validated['is_active'] = (bool)($validated['is_active'] ?? true);
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();
        
            $category = BeritaCategory::create($validated);
        
            Log::info('Berita category created successfully', [
                'category_id' => $category->id,
                'category_name' => $category->category_name,
                'category_slug' => $category->category_slug,
                'is_active' => $category->is_active,
            ]);

            // Write activity log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Created BeritaCategory: ' . ($category->category_name ?? 'Untitled') . ' (ID: ' . $category->id . ')',
            ]);
        
            return redirect()->route('berita-categories.index')->with('status', 'Kategori dibuat.');
        } catch (ValidationException $e) {
            Log::warning('Validation failed when creating berita category', [
                'errors' => $e->errors(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            Log::error('Error creating berita category', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while creating the category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BeritaCategory $beritaCategory)
    {
        // Load relationships
        $beritaCategory->load(['createdBy', 'updatedBy']);

        return Inertia::render('BeritaCategories/Show', [
            'category' => [
                'id' => $beritaCategory->id,
                'category_name' => $beritaCategory->category_name,
                'category_slug' => $beritaCategory->category_slug,
                'is_active' => (bool) $beritaCategory->is_active,
                'created_at' => $beritaCategory->created_at?->toDateTimeString(),
                'updated_at' => $beritaCategory->updated_at?->toDateTimeString(),
                'created_by' => $beritaCategory->createdBy ? [
                    'id' => $beritaCategory->createdBy->id,
                    'name' => $beritaCategory->createdBy->name,
                ] : null,
                'updated_by' => $beritaCategory->updatedBy ? [
                    'id' => $beritaCategory->updatedBy->id,
                    'name' => $beritaCategory->updatedBy->name,
                ] : null,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BeritaCategory $beritaCategory)
    {
        return Inertia::render('BeritaCategories/Edit', [
            'category' => [
                'id' => $beritaCategory->id,
                'category_name' => $beritaCategory->category_name,
                'category_slug' => $beritaCategory->category_slug,
                'is_active' => (bool) $beritaCategory->is_active,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BeritaCategory $beritaCategory)
    {
        try {
            Log::info('Updating berita category', [
                'user_id' => Auth::id() ?? 'guest',
                'category_id' => $beritaCategory->id,
                'category_name' => $request->input('category_name'),
            ]);

            $validated = $request->validate([
                'category_name' => ['required', 'string', 'max:255', 'unique:berita_categories,category_name,' . $beritaCategory->id],
                'category_slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:berita_categories,category_slug,' . $beritaCategory->id],
                'is_active' => ['nullable', 'boolean'],
            ], [
                'category_name.required' => 'Nama kategori wajib diisi.',
                'category_name.string' => 'Nama kategori harus berupa teks.',
                'category_name.max' => 'Nama kategori maksimal 255 karakter.',
                'category_name.unique' => 'Nama kategori sudah digunakan.',
                'category_slug.required' => 'Slug kategori wajib diisi.',
                'category_slug.string' => 'Slug kategori harus berupa teks.',
                'category_slug.max' => 'Slug kategori maksimal 255 karakter.',
                'category_slug.regex' => 'Slug hanya boleh huruf kecil, angka, dan tanda minus (-).',
                'category_slug.unique' => 'Slug kategori sudah digunakan.',
                'is_active.boolean' => 'Status aktif harus bernilai benar/salah.',
            ]);
        
            $validated['is_active'] = (bool)($validated['is_active'] ?? true);
            $validated['updated_by'] = Auth::id();
        
            $beritaCategory->update($validated);
        
            Log::info('Berita category updated successfully', [
                'category_id' => $beritaCategory->id,
                'category_name' => $beritaCategory->category_name,
                'category_slug' => $beritaCategory->category_slug,
                'is_active' => $beritaCategory->is_active,
            ]);

            // Write activity log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Updated BeritaCategory: ' . ($beritaCategory->category_name ?? 'Untitled') . ' (ID: ' . $beritaCategory->id . ')',
            ]);
        
            return redirect()->route('berita-categories.index')->with('status', 'Kategori diperbarui.');
        } catch (ValidationException $e) {
            Log::warning('Validation failed when updating berita category', [
                'category_id' => $beritaCategory->id,
                'errors' => $e->errors(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            Log::error('Error updating berita category', [
                'category_id' => $beritaCategory->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while updating the category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BeritaCategory $beritaCategory)
    {
        try {
            Log::info('Deleting berita category', [
                'user_id' => Auth::id() ?? 'guest',
                'category_id' => $beritaCategory->id,
                'category_name' => $beritaCategory->category_name,
            ]);

            $beritaCategory->delete();

            Log::info('Berita category deleted successfully', [
                'category_id' => $beritaCategory->id,
                'category_name' => $beritaCategory->category_name,
            ]);

            // Write activity log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Deleted BeritaCategory: ' . ($beritaCategory->category_name ?? 'Untitled') . ' (ID: ' . $beritaCategory->id . ')',
            ]);

            return redirect()->route('berita-categories.index')->with('status', 'Kategori dihapus.');
        } catch (Exception $e) {
            Log::error('Error deleting berita category', [
                'category_id' => $beritaCategory->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the category: ' . $e->getMessage());
        }
    }
}
