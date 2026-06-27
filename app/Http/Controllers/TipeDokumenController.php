<?php

namespace App\Http\Controllers;

use App\Models\TipeDokumen;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TipeDokumenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = TipeDokumen::ordered()->get();
        
        return Inertia::render('Settings/TipeDokumen/Index', [
            'items' => $items,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tipe_dokumen,name',
            'order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        TipeDokumen::create([
            'name' => $validated['name'],
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('tipe-dokumen.index')->with('success', 'Tipe dokumen berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipeDokumen $tipeDokumen)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tipe_dokumen,name,' . $tipeDokumen->id,
            'order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $tipeDokumen->update($validated);

        return redirect()->route('tipe-dokumen.index')->with('success', 'Tipe dokumen berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipeDokumen $tipeDokumen)
    {
        $tipeDokumen->delete();
        
        return redirect()->route('tipe-dokumen.index')->with('success', 'Tipe dokumen berhasil dihapus');
    }
}
