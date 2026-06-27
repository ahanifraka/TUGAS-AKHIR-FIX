<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PermohonanInformasi;
use App\Models\PermohonanInformasiLog;
use App\Models\PengajuanKeberatan;
use App\Models\PengajuanKeberatanLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class PPIDAdminController extends Controller
{
    // Permohonan Informasi Methods
    public function permohonanInformasiIndex(Request $request)
    {
        $query = PermohonanInformasi::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_telepon_email', 'like', "%{$search}%")
                  ->orWhere('rincian_informasi', 'like', "%{$search}%")
                  ->orWhere('kode_unik', 'like', "%{$search}%");
            });
        }

        $permohonan = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/PPID/PermohonanInformasi/Index', [
            'permohonan' => $permohonan,
            'filters' => $request->only(['status', 'search']),
            'stats' => [
                'total' => PermohonanInformasi::count(),
                'pending' => PermohonanInformasi::where('status', 'pending')->count(),
                'processed' => PermohonanInformasi::where('status', 'processed')->count(),
                'completed' => PermohonanInformasi::where('status', 'completed')->count(),
                'rejected' => PermohonanInformasi::where('status', 'rejected')->count(),
            ]
        ]);
    }

    public function permohonanInformasiShow(PermohonanInformasi $permohonanInformasi)
    {
        $permohonanInformasi->load('logs.user');
        
        return Inertia::render('Admin/PPID/PermohonanInformasi/Show', [
            'permohonan' => $permohonanInformasi
        ]);
    }

    public function permohonanInformasiUpdate(Request $request, PermohonanInformasi $permohonanInformasi)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processed,completed,rejected',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $permohonanInformasi->status;
        $oldNotes = $permohonanInformasi->admin_notes;
        
        $permohonanInformasi->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
            'processed_at' => now()
        ]);

        // Log status change
        if ($oldStatus !== $validated['status']) {
            PermohonanInformasiLog::create([
                'permohonan_informasi_id' => $permohonanInformasi->id,
                'action' => 'status_changed',
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'notes' => $validated['admin_notes'],
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
            ]);
        }
        
        // Log note addition/change if notes changed
        if ($oldNotes !== $validated['admin_notes'] && !empty($validated['admin_notes'])) {
            PermohonanInformasiLog::create([
                'permohonan_informasi_id' => $permohonanInformasi->id,
                'action' => 'note_added',
                'old_status' => $validated['status'],
                'new_status' => $validated['status'],
                'notes' => $validated['admin_notes'],
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
            ]);
        }

        return redirect()->back()->with('success', 'Status permohonan berhasil diperbarui.');
    }

    public function downloadAttachment(PermohonanInformasi $permohonanInformasi)
    {
        if (Storage::disk('local')->exists($permohonanInformasi->lampiran_path)) {
            return response()->download(Storage::disk('local')->path($permohonanInformasi->lampiran_path));
        }

        if (Storage::disk('public')->exists($permohonanInformasi->lampiran_path)) {
            return response()->download(Storage::disk('public')->path($permohonanInformasi->lampiran_path));
        }

        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    // Pengajuan Keberatan Methods
    public function pengajuanKeberatanIndex(Request $request)
    {
        $query = PengajuanKeberatan::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%")
                  ->orWhere('nomor_induk_kependudukan', 'like', "%{$search}%")
                  ->orWhere('kode_unik', 'like', "%{$search}%");
            });
        }

        $pengajuan = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/PPID/PengajuanKeberatan/Index', [
            'pengajuan' => $pengajuan,
            'filters' => $request->only(['status', 'search']),
            'stats' => [
                'total' => PengajuanKeberatan::count(),
                'pending' => PengajuanKeberatan::where('status', 'pending')->count(),
                'processed' => PengajuanKeberatan::where('status', 'processed')->count(),
                'completed' => PengajuanKeberatan::where('status', 'completed')->count(),
                'rejected' => PengajuanKeberatan::where('status', 'rejected')->count(),
            ]
        ]);
    }

    public function pengajuanKeberatanShow(PengajuanKeberatan $pengajuanKeberatan)
    {
        $pengajuanKeberatan->load('logs.user');

        return Inertia::render('Admin/PPID/PengajuanKeberatan/Show', [
            'pengajuan' => $pengajuanKeberatan
        ]);
    }

    public function pengajuanKeberatanUpdate(Request $request, PengajuanKeberatan $pengajuanKeberatan)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processed,completed,rejected',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $pengajuanKeberatan->status;
        $oldNotes = $pengajuanKeberatan->admin_notes;

        $pengajuanKeberatan->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
            'processed_at' => now()
        ]);

        // Log status change
        if ($oldStatus !== $validated['status']) {
            PengajuanKeberatanLog::create([
                'pengajuan_keberatan_id' => $pengajuanKeberatan->id,
                'action' => 'status_changed',
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'notes' => $validated['admin_notes'],
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
            ]);
        }
        
        // Log note addition/change if notes changed
        if ($oldNotes !== $validated['admin_notes'] && !empty($validated['admin_notes'])) {
            PengajuanKeberatanLog::create([
                'pengajuan_keberatan_id' => $pengajuanKeberatan->id,
                'action' => 'note_added',
                'old_status' => $validated['status'],
                'new_status' => $validated['status'],
                'notes' => $validated['admin_notes'],
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
            ]);
        }

        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    // Dashboard method
    public function dashboard()
    {
        $permohonanStats = [
            'total' => PermohonanInformasi::count(),
            'pending' => PermohonanInformasi::where('status', 'pending')->count(),
            'processed' => PermohonanInformasi::where('status', 'processed')->count(),
            'completed' => PermohonanInformasi::where('status', 'completed')->count(),
            'rejected' => PermohonanInformasi::where('status', 'rejected')->count(),
        ];

        $keberatanStats = [
            'total' => PengajuanKeberatan::count(),
            'pending' => PengajuanKeberatan::where('status', 'pending')->count(),
            'processed' => PengajuanKeberatan::where('status', 'processed')->count(),
            'completed' => PengajuanKeberatan::where('status', 'completed')->count(),
            'rejected' => PengajuanKeberatan::where('status', 'rejected')->count(),
        ];

        $recentPermohonan = PermohonanInformasi::latest()->take(5)->get();
        $recentKeberatan = PengajuanKeberatan::latest()->take(5)->get();

        return Inertia::render('Admin/PPID/Dashboard', [
            'permohonanStats' => $permohonanStats,
            'keberatanStats' => $keberatanStats,
            'recentPermohonan' => $recentPermohonan,
            'recentKeberatan' => $recentKeberatan,
        ]);
    }
}
