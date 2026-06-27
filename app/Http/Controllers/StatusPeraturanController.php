<?php

namespace App\Http\Controllers;

use App\Models\StatusPeraturan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatusPeraturanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = StatusPeraturan::ordered()->get();
        
        return Inertia::render('Settings/StatusPeraturan/Index', [
            'items' => $items,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:status_peraturan,name',
            'order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        StatusPeraturan::create([
            'name' => $validated['name'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('status-peraturan.index')->with('success', 'Status peraturan berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StatusPeraturan $statusPeraturan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:status_peraturan,name,' . $statusPeraturan->id,
            'order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $statusPeraturan->update($validated);

        return redirect()->route('status-peraturan.index')->with('success', 'Status peraturan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StatusPeraturan $statusPeraturan)
    {
        $statusPeraturan->delete();
        
        return redirect()->route('status-peraturan.index')->with('success', 'Status peraturan berhasil dihapus');
    }
}
