<?php

namespace App\Http\Controllers;

use App\Models\Pejabat;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PejabatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Pejabat::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%')
                    ->orWhere('pendidikan', 'like', '%' . $search . '%')
                    ->orWhere('ttl', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%')
                    ->orWhere('pendidikan', 'like', '%' . $search . '%')
                    ->orWhere('ttl', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $pejabats = $query->latest()->orderBy('created_at')->paginate(10)->withQueryString();

        $data = [
            'data' => $pejabats->getCollection()->transform(function ($p) {
                return [
                    'id' => $p->id,
                    'nama' => $p->nama,
                    'jabatan' => $p->jabatan,
                    'image_url' => $p->image_url,
                    'published' => (bool) $p->published,
                    'updated_at' => $p->updated_at?->toDateTimeString(),
                ];
            }),
            'links' => [
                'first' => $pejabats->url(1),
                'last' => $pejabats->url($pejabats->lastPage()),
                'prev' => $pejabats->previousPageUrl(),
                'next' => $pejabats->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $pejabats->currentPage(),
                'last_page' => $pejabats->lastPage(),
                'per_page' => $pejabats->perPage(),
                'total' => $pejabats->total(),
            ],
        ];

        return Inertia::render('Pejabats/Index', [
            'pejabats' => $data,
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
        return Inertia::render('Pejabats/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'ttl' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string'],
            'pendidikan' => ['nullable', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer'],
            'published' => ['nullable', 'boolean'],
        ]);

        $validated['published'] = (bool) ($validated['published'] ?? true);

        $pejabat = Pejabat::create($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Created pejabat: ' . $pejabat->nama,
        ]);

        return redirect()->route('pejabats.index')->with('status', 'Pejabat created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pejabat $pejabat)
    {
        return Inertia::render('Pejabats/Show', [
            'pejabat' => [
                'id' => $pejabat->id,
                'nama' => $pejabat->nama,
                'ttl' => $pejabat->ttl,
                'image' => $pejabat->image,
                'image_url' => $pejabat->image_url,
                'pendidikan' => $pejabat->pendidikan,
                'jabatan' => $pejabat->jabatan,
                'description' => $pejabat->description,
                'order' => $pejabat->order,
                'published' => (bool) $pejabat->published,
                'created_at' => $pejabat->created_at?->toDateTimeString(),
                'updated_at' => $pejabat->updated_at?->toDateTimeString(),
            ],
        ]);
    }

    public function showPublic($id)
    {
        $pejabat = Pejabat::findOrFail($id);

        return Inertia::render('Profil/DetailPejabat', [
            'pejabat' => $pejabat
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pejabat $pejabat)
    {
        return Inertia::render('Pejabats/Edit', [
            'pejabat' => [
                'id' => $pejabat->id,
                'nama' => $pejabat->nama,
                'ttl' => $pejabat->ttl,
                'image' => $pejabat->image,
                'image_url' => $pejabat->image_url,
                'pendidikan' => $pejabat->pendidikan,
                'jabatan' => $pejabat->jabatan,
                'description' => $pejabat->description,
                'order' => $pejabat->order,
                'published' => (bool) $pejabat->published,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pejabat $pejabat)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'ttl' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string'],
            'pendidikan' => ['nullable', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['nullable', 'integer'],
            'published' => ['nullable', 'boolean'],
        ]);

        $validated['published'] = (bool) ($validated['published'] ?? true);

        $pejabat->update($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Updated pejabat: ' . $pejabat->nama,
        ]);

        return redirect()->route('pejabats.index')->with('status', 'Pejabat updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pejabat $pejabat)
    {
        $pejabatNama = $pejabat->nama;
        $pejabat->delete();
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Deleted pejabat: ' . $pejabatNama,
        ]);

        return redirect()->route('pejabats.index')->with('status', 'Pejabat deleted.');
    }

    public function togglePublish(Pejabat $pejabat)
    {
        $pejabat->published = !$pejabat->published;
        $pejabat->published = !$pejabat->published;
        $pejabat->save();

        return redirect()->back()->with('status', 'Status publish ' . $pejabat->nama . ' diperbarui.');
    }

    /**
     * Show the reorder interface.
     */
    public function reorder()
    {
        $pejabats = Pejabat::where('published', true)->orderBy('order', 'asc')->orderBy('id', 'asc')->get();

        return Inertia::render('Pejabats/Reorder', [
            'pejabats' => $pejabats->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nama' => $p->nama,
                    'jabatan' => $p->jabatan,
                    'image_url' => $p->image_url,
                    'order' => $p->order,
                ];
            }),
        ]);
    }

    /**
     * Update the order of pejabats.
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'pejabats' => ['required', 'array'],
            'pejabats.*.id' => ['required', 'integer', 'exists:pejabat,id'],
            'pejabats.*.order' => ['required', 'integer'],
        ]);

        foreach ($validated['pejabats'] as $pejabatData) {
            Pejabat::where('id', $pejabatData['id'])
                ->update(['order' => $pejabatData['order']]);
        }

        return redirect()->route('pejabats.index')->with('status', 'Urutan pejabat berhasil diperbarui.');
    }
}