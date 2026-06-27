<?php

namespace App\Http\Controllers;

use App\Models\Regulasi;
use App\Models\TipeDokumen;
use App\Models\StatusPeraturan;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RegulasiController extends Controller
{
    /**
     * Public: Display detail of a regulation.
     */
    public function showPublic(Regulasi $regulasi)
    {
        // Only show active regulations to public
        if (!$regulasi->is_active) {
            abort(404);
        }

        return Inertia::render('Regulasi/Detail', [
            'item' => [
                'id' => $regulasi->id,
                'title' => $regulasi->title,
                'content' => $regulasi->content,
                'file' => $regulasi->file,
                'file_url' => $regulasi->file_url,
                'tipe_dokumen' => $regulasi->tipe_dokumen,
                'judul_peraturan' => $regulasi->judul_peraturan,
                'nomor_peraturan' => $regulasi->nomor_peraturan,
                'tahun_peraturan' => $regulasi->tahun_peraturan,
                'jenis_peraturan' => $regulasi->jenis_peraturan,
                'singkatan_jenis' => $regulasi->singkatan_jenis,
                'tempat_penetapan' => $regulasi->tempat_penetapan,
                'tanggal_penetapan' => $regulasi->tanggal_penetapan?->format('d F Y'),
                'tanggal_pengundangan' => $regulasi->tanggal_pengundangan?->format('d F Y'),
                'sumber' => $regulasi->sumber,
                'subjek' => $regulasi->subjek,
                'status_peraturan' => $regulasi->status_peraturan,
                'keterangan_dokumen' => $regulasi->keterangan_dokumen,
                'teu_badan' => $regulasi->teu_badan,
                'bidang_hukum' => $regulasi->bidang_hukum,
                'bahasa' => $regulasi->bahasa,
                'lokasi' => $regulasi->lokasi,
                'keterangan_status' => $regulasi->keterangan_status,
                'tag' => $regulasi->tag,
                'created_at' => $regulasi->created_at?->format('d F Y'),
                'updated_at' => $regulasi->updated_at?->format('d F Y H:i'),
            ],
        ]);
    }

    /**
     * Public: Display active regulations list.
     */
    public function indexPublic(Request $request)
    {
        $search = $request->query('search');
        $tipeDokumen = $request->query('tipe_dokumen');
        $nomorPeraturan = $request->query('nomor_peraturan');
        $tahunPeraturan = $request->query('tahun_peraturan');
        $statusPeraturan = $request->query('status_peraturan');
        
        $query = Regulasi::where('is_active', true);
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%')
                  ->orWhere('judul_peraturan', 'like', '%' . $search . '%')
                  ->orWhere('nomor_peraturan', 'like', '%' . $search . '%');
            });
        }
        
        if ($tipeDokumen) {
            $query->where('tipe_dokumen', $tipeDokumen);
        }
        
        if ($nomorPeraturan) {
            $query->where('nomor_peraturan', 'like', '%' . $nomorPeraturan . '%');
        }
        
        if ($tahunPeraturan) {
            $query->where('tahun_peraturan', $tahunPeraturan);
        }
        
        if ($statusPeraturan) {
            $query->where('status_peraturan', $statusPeraturan);
        }

        $items = $query->latest('updated_at')->paginate(12)->withQueryString();

        $data = [
            'data' => $items->getCollection()->transform(function ($r) {
                return [
                    'id' => $r->id,
                    'title' => $r->title,
                    'content' => $r->content,
                    'file' => $r->file,
                    'file_url' => $r->file_url,
                    'tipe_dokumen' => $r->tipe_dokumen,
                    'judul_peraturan' => $r->judul_peraturan,
                    'nomor_peraturan' => $r->nomor_peraturan,
                    'tahun_peraturan' => $r->tahun_peraturan,
                    'jenis_peraturan' => $r->jenis_peraturan,
                    'singkatan_jenis' => $r->singkatan_jenis,
                    'tempat_penetapan' => $r->tempat_penetapan,
                    'tanggal_penetapan' => $r->tanggal_penetapan?->format('Y-m-d'),
                    'tanggal_pengundangan' => $r->tanggal_pengundangan?->format('Y-m-d'),
                    'sumber' => $r->sumber,
                    'subjek' => $r->subjek,
                    'status_peraturan' => $r->status_peraturan,
                    'keterangan_dokumen' => $r->keterangan_dokumen,
                    'teu_badan' => $r->teu_badan,
                    'bidang_hukum' => $r->bidang_hukum,
                    'bahasa' => $r->bahasa,
                    'lokasi' => $r->lokasi,
                    'keterangan_status' => $r->keterangan_status,
                    'tag' => $r->tag,
                    'updated_at' => $r->updated_at?->toDateTimeString(),
                ];
            }),
            'links' => [
                'first' => $items->url(1),
                'last' => $items->url($items->lastPage()),
                'prev' => $items->previousPageUrl(),
                'next' => $items->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ];

        return Inertia::render('Regulasi/Regulasi', [
            'items' => $data,
            'filters' => [
                'search' => $search,
                'tipe_dokumen' => $tipeDokumen,
                'nomor_peraturan' => $nomorPeraturan,
                'tahun_peraturan' => $tahunPeraturan,
                'status_peraturan' => $statusPeraturan,
            ],
            'tipeDokumenOptions' => TipeDokumen::active()->ordered()->pluck('name')->toArray(),
            'statusPeraturanOptions' => StatusPeraturan::active()->ordered()->pluck('name')->toArray(),
        ]);
    }

    /**
     * Admin: Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'updated_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['title', 'is_active', 'updated_at', 'created_at', 'nomor_peraturan', 'tahun_peraturan'];
        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'updated_at';
        }
        $perPage = (int) $request->query('per_page', 10);
        $allowedPerPage = [5, 10, 20, 50];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $query = Regulasi::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%')
                  ->orWhere('judul_peraturan', 'like', '%' . $search . '%')
                  ->orWhere('nomor_peraturan', 'like', '%' . $search . '%');
            });
        }

        $items = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();
        $data = [
            'data' => $items->getCollection()->transform(function ($r) {
                return [
                    'id' => $r->id,
                    'title' => $r->title,
                    'is_active' => (bool) $r->is_active,
                    'file' => $r->file,
                    'file_url' => $r->file_url,
                    'tipe_dokumen' => $r->tipe_dokumen,
                    'judul_peraturan' => $r->judul_peraturan,
                    'nomor_peraturan' => $r->nomor_peraturan,
                    'tahun_peraturan' => $r->tahun_peraturan,
                    'status_peraturan' => $r->status_peraturan,
                    'teu_badan' => $r->teu_badan,
                    'bidang_hukum' => $r->bidang_hukum,
                    'bahasa' => $r->bahasa,
                    'lokasi' => $r->lokasi,
                    'keterangan_status' => $r->keterangan_status,
                    'tag' => $r->tag,
                    'updated_at' => $r->updated_at?->toDateTimeString(),
                ];
            }),
            'links' => [
                'first' => $items->url(1),
                'last' => $items->url($items->lastPage()),
                'prev' => $items->previousPageUrl(),
                'next' => $items->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ];

        return Inertia::render('Regulasi/Index', [
            'items' => $data,
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
        return Inertia::render('Regulasi/Create', [
            'tipeDokumenOptions' => TipeDokumen::active()->ordered()->pluck('name')->toArray(),
            'statusPeraturanOptions' => StatusPeraturan::active()->ordered()->pluck('name')->toArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tipeDokumenNames = TipeDokumen::active()->pluck('name')->toArray();
        $statusPeraturanNames = StatusPeraturan::active()->pluck('name')->toArray();
        
        $messages = [
            // Basic Fields
            'title.required' => 'Judul regulasi wajib diisi.',
            'title.string' => 'Judul harus berupa teks.',
            'title.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            
            'content.required' => 'Konten regulasi wajib diisi.',
            'content.string' => 'Konten harus berupa teks.',
            
            // File
            'file.file' => 'File yang diunggah harus berupa file yang valid.',
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'Ukuran file tidak boleh lebih dari 10MB.',
            
            // Status
            'is_active.boolean' => 'Status aktif harus berupa ya atau tidak.',
            
            // Tipe Dokumen
            'tipe_dokumen.string' => 'Tipe dokumen harus berupa teks.',
            'tipe_dokumen.in' => 'Tipe dokumen yang dipilih tidak valid.',
            
            // Peraturan Details
            'judul_peraturan.string' => 'Judul peraturan harus berupa teks.',
            'judul_peraturan.max' => 'Judul peraturan tidak boleh lebih dari 255 karakter.',
            
            'nomor_peraturan.required' => 'Nomor peraturan wajib diisi.',
            'nomor_peraturan.string' => 'Nomor peraturan harus berupa teks.',
            'nomor_peraturan.max' => 'Nomor peraturan tidak boleh lebih dari 255 karakter.',
            
            'tahun_peraturan.required' => 'Tahun peraturan wajib diisi.',
            'tahun_peraturan.string' => 'Tahun peraturan harus berupa teks.',
            'tahun_peraturan.max' => 'Tahun peraturan tidak boleh lebih dari 4 karakter.',
            
            'jenis_peraturan.required' => 'Jenis peraturan wajib diisi.',
            'jenis_peraturan.string' => 'Jenis peraturan harus berupa teks.',
            'jenis_peraturan.max' => 'Jenis peraturan tidak boleh lebih dari 255 karakter.',
            
            'singkatan_jenis.string' => 'Singkatan jenis harus berupa teks.',
            'singkatan_jenis.max' => 'Singkatan jenis tidak boleh lebih dari 255 karakter.',
            
            // Tempat & Tanggal
            'tempat_penetapan.string' => 'Tempat penetapan harus berupa teks.',
            'tempat_penetapan.max' => 'Tempat penetapan tidak boleh lebih dari 255 karakter.',
            
            'tanggal_penetapan.date' => 'Tanggal penetapan harus berupa tanggal yang valid.',
            'tanggal_pengundangan.date' => 'Tanggal pengundangan harus berupa tanggal yang valid.',
            
            // Additional Info
            'sumber.string' => 'Sumber harus berupa teks.',
            'sumber.max' => 'Sumber tidak boleh lebih dari 255 karakter.',
            
            'subjek.string' => 'Subjek harus berupa teks.',
            
            'status_peraturan.string' => 'Status peraturan harus berupa teks.',
            'status_peraturan.in' => 'Status peraturan yang dipilih tidak valid.',
            
            'keterangan_dokumen.string' => 'Keterangan dokumen harus berupa teks.',
            
            'teu_badan.string' => 'TEU Badan harus berupa teks.',
            'teu_badan.max' => 'TEU Badan tidak boleh lebih dari 255 karakter.',
            
            'bidang_hukum.string' => 'Bidang hukum harus berupa teks.',
            'bidang_hukum.max' => 'Bidang hukum tidak boleh lebih dari 255 karakter.',
            
            'bahasa.string' => 'Bahasa harus berupa teks.',
            'bahasa.max' => 'Bahasa tidak boleh lebih dari 255 karakter.',
            
            'lokasi.string' => 'Lokasi harus berupa teks.',
            'lokasi.max' => 'Lokasi tidak boleh lebih dari 255 karakter.',
            
            'keterangan_status.string' => 'Keterangan status harus berupa teks.',
            
            'tag.string' => 'Tag harus berupa teks.',
        ];
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'is_active' => 'sometimes|boolean',
            'tipe_dokumen' => 'nullable|string|in:' . implode(',', $tipeDokumenNames),
            'judul_peraturan' => 'nullable|string|max:255',
            'nomor_peraturan' => 'required|string|max:255',
            'tahun_peraturan' => 'required|string|max:4',
            'jenis_peraturan' => 'required|string|max:255',
            'singkatan_jenis' => 'nullable|string|max:255',
            'tempat_penetapan' => 'nullable|string|max:255',
            'tanggal_penetapan' => 'nullable|date',
            'tanggal_pengundangan' => 'nullable|date',
            'sumber' => 'nullable|string|max:255',
            'subjek' => 'nullable|string',
            'status_peraturan' => 'nullable|string|in:' . implode(',', $statusPeraturanNames),
            'keterangan_dokumen' => 'nullable|string',
            'teu_badan' => 'nullable|string|max:255',
            'bidang_hukum' => 'nullable|string|max:255',
            'bahasa' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'keterangan_status' => 'nullable|string',
            'tag' => 'nullable|string',
        ], $messages);

        $regulasi = new Regulasi();
        $regulasi->fill($validated);
        $regulasi->is_active = (bool) ($validated['is_active'] ?? false);
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $extension = $uploadedFile->getClientOriginalExtension();
            
            // Generate custom filename: [Tahun][Jenis Regulasi][Nomor Regulasi]
            $tahun = preg_replace('/[^0-9]/', '', $validated['tahun_peraturan']);
            $jenis = preg_replace('/[^a-zA-Z0-9]/', '', $validated['jenis_peraturan']);
            $nomor = preg_replace('/[^a-zA-Z0-9]/', '', $validated['nomor_peraturan']);
            $customFilename = $tahun . $jenis . $nomor . '.' . $extension;
            
            // Store with custom filename
            $path = $uploadedFile->storeAs('regulasi', $customFilename, 'public');
            $regulasi->file = 'storage/' . $path;
        }
        $regulasi->save();

        Log::info('Regulasi created', ['id' => $regulasi->id, 'title' => $regulasi->title, 'user_id' => $request->user()?->id]);

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'activity' => 'Created Regulasi: ' . $regulasi->title,
        ]);

        return redirect()->route('regulasis.index')->with('success', 'Regulasi berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Regulasi $regulasi)
    {
        return Inertia::render('Regulasi/Show', [
            'item' => [
                'id' => $regulasi->id,
                'title' => $regulasi->title,
                'content' => $regulasi->content,
                'file' => $regulasi->file,
                'file_url' => $regulasi->file_url,
                'is_active' => (bool) $regulasi->is_active,
                'tipe_dokumen' => $regulasi->tipe_dokumen,
                'judul_peraturan' => $regulasi->judul_peraturan,
                'nomor_peraturan' => $regulasi->nomor_peraturan,
                'tahun_peraturan' => $regulasi->tahun_peraturan,
                'jenis_peraturan' => $regulasi->jenis_peraturan,
                'singkatan_jenis' => $regulasi->singkatan_jenis,
                'tempat_penetapan' => $regulasi->tempat_penetapan,
                'tanggal_penetapan' => $regulasi->tanggal_penetapan?->format('Y-m-d'),
                'tanggal_pengundangan' => $regulasi->tanggal_pengundangan?->format('Y-m-d'),
                'sumber' => $regulasi->sumber,
                'subjek' => $regulasi->subjek,
                'status_peraturan' => $regulasi->status_peraturan,
                'keterangan_dokumen' => $regulasi->keterangan_dokumen,
                'teu_badan' => $regulasi->teu_badan,
                'bidang_hukum' => $regulasi->bidang_hukum,
                'bahasa' => $regulasi->bahasa,
                'lokasi' => $regulasi->lokasi,
                'keterangan_status' => $regulasi->keterangan_status,
                'tag' => $regulasi->tag,
                'updated_at' => $regulasi->updated_at?->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Regulasi $regulasi)
    {
        return Inertia::render('Regulasi/Edit', [
            'item' => [
                'id' => $regulasi->id,
                'title' => $regulasi->title,
                'content' => $regulasi->content,
                'file' => $regulasi->file,
                'file_url' => $regulasi->file_url,
                'is_active' => (bool) $regulasi->is_active,
                'tipe_dokumen' => $regulasi->tipe_dokumen,
                'judul_peraturan' => $regulasi->judul_peraturan,
                'nomor_peraturan' => $regulasi->nomor_peraturan,
                'tahun_peraturan' => $regulasi->tahun_peraturan,
                'jenis_peraturan' => $regulasi->jenis_peraturan,
                'singkatan_jenis' => $regulasi->singkatan_jenis,
                'tempat_penetapan' => $regulasi->tempat_penetapan,
                'tanggal_penetapan' => $regulasi->tanggal_penetapan?->toDateString(),
                'tanggal_pengundangan' => $regulasi->tanggal_pengundangan?->toDateString(),
                'sumber' => $regulasi->sumber,
                'subjek' => $regulasi->subjek,
                'status_peraturan' => $regulasi->status_peraturan,
                'keterangan_dokumen' => $regulasi->keterangan_dokumen,
                'teu_badan' => $regulasi->teu_badan,
                'bidang_hukum' => $regulasi->bidang_hukum,
                'bahasa' => $regulasi->bahasa,
                'lokasi' => $regulasi->lokasi,
                'keterangan_status' => $regulasi->keterangan_status,
                'tag' => $regulasi->tag,
            ],
            'tipeDokumenOptions' => TipeDokumen::active()->ordered()->pluck('name')->toArray(),
            'statusPeraturanOptions' => StatusPeraturan::active()->ordered()->pluck('name')->toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Regulasi $regulasi)
    {
        $tipeDokumenNames = TipeDokumen::active()->pluck('name')->toArray();
        $statusPeraturanNames = StatusPeraturan::active()->pluck('name')->toArray();
        
        $messages = [
            // Basic Fields
            'title.required' => 'Judul regulasi wajib diisi.',
            'title.string' => 'Judul harus berupa teks.',
            'title.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            
            'content.required' => 'Konten regulasi wajib diisi.',
            'content.string' => 'Konten harus berupa teks.',
            
            // File
            'file.file' => 'File yang diunggah harus berupa file yang valid.',
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'Ukuran file tidak boleh lebih dari 10MB.',
            
            // Status
            'is_active.boolean' => 'Status aktif harus berupa ya atau tidak.',
            
            // Tipe Dokumen
            'tipe_dokumen.string' => 'Tipe dokumen harus berupa teks.',
            'tipe_dokumen.in' => 'Tipe dokumen yang dipilih tidak valid.',
            
            // Peraturan Details
            'judul_peraturan.string' => 'Judul peraturan harus berupa teks.',
            'judul_peraturan.max' => 'Judul peraturan tidak boleh lebih dari 255 karakter.',
            
            'nomor_peraturan.required' => 'Nomor peraturan wajib diisi.',
            'nomor_peraturan.string' => 'Nomor peraturan harus berupa teks.',
            'nomor_peraturan.max' => 'Nomor peraturan tidak boleh lebih dari 255 karakter.',
            
            'tahun_peraturan.required' => 'Tahun peraturan wajib diisi.',
            'tahun_peraturan.string' => 'Tahun peraturan harus berupa teks.',
            'tahun_peraturan.max' => 'Tahun peraturan tidak boleh lebih dari 4 karakter.',
            
            'jenis_peraturan.required' => 'Jenis peraturan wajib diisi.',
            'jenis_peraturan.string' => 'Jenis peraturan harus berupa teks.',
            'jenis_peraturan.max' => 'Jenis peraturan tidak boleh lebih dari 255 karakter.',
            
            'singkatan_jenis.string' => 'Singkatan jenis harus berupa teks.',
            'singkatan_jenis.max' => 'Singkatan jenis tidak boleh lebih dari 255 karakter.',
            
            // Tempat & Tanggal
            'tempat_penetapan.string' => 'Tempat penetapan harus berupa teks.',
            'tempat_penetapan.max' => 'Tempat penetapan tidak boleh lebih dari 255 karakter.',
            
            'tanggal_penetapan.date' => 'Tanggal penetapan harus berupa tanggal yang valid.',
            'tanggal_pengundangan.date' => 'Tanggal pengundangan harus berupa tanggal yang valid.',
            
            // Additional Info
            'sumber.string' => 'Sumber harus berupa teks.',
            'sumber.max' => 'Sumber tidak boleh lebih dari 255 karakter.',
            
            'subjek.string' => 'Subjek harus berupa teks.',
            
            'status_peraturan.string' => 'Status peraturan harus berupa teks.',
            'status_peraturan.in' => 'Status peraturan yang dipilih tidak valid.',
            
            'keterangan_dokumen.string' => 'Keterangan dokumen harus berupa teks.',
            
            'teu_badan.string' => 'TEU Badan harus berupa teks.',
            'teu_badan.max' => 'TEU Badan tidak boleh lebih dari 255 karakter.',
            
            'bidang_hukum.string' => 'Bidang hukum harus berupa teks.',
            'bidang_hukum.max' => 'Bidang hukum tidak boleh lebih dari 255 karakter.',
            
            'bahasa.string' => 'Bahasa harus berupa teks.',
            'bahasa.max' => 'Bahasa tidak boleh lebih dari 255 karakter.',
            
            'lokasi.string' => 'Lokasi harus berupa teks.',
            'lokasi.max' => 'Lokasi tidak boleh lebih dari 255 karakter.',
            
            'keterangan_status.string' => 'Keterangan status harus berupa teks.',
            
            'tag.string' => 'Tag harus berupa teks.',
        ];
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'is_active' => 'sometimes|boolean',
            'tipe_dokumen' => 'nullable|string|in:' . implode(',', $tipeDokumenNames),
            'judul_peraturan' => 'nullable|string|max:255',
            'nomor_peraturan' => 'required|string|max:255',
            'tahun_peraturan' => 'required|string|max:4',
            'jenis_peraturan' => 'required|string|max:255',
            'singkatan_jenis' => 'nullable|string|max:255',
            'tempat_penetapan' => 'nullable|string|max:255',
            'tanggal_penetapan' => 'nullable|date',
            'tanggal_pengundangan' => 'nullable|date',
            'sumber' => 'nullable|string|max:255',
            'subjek' => 'nullable|string',
            'status_peraturan' => 'nullable|string|in:' . implode(',', $statusPeraturanNames),
            'keterangan_dokumen' => 'nullable|string',
            'teu_badan' => 'nullable|string|max:255',
            'bidang_hukum' => 'nullable|string|max:255',
            'bahasa' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'keterangan_status' => 'nullable|string',
            'tag' => 'nullable|string',
        ], $messages);

        // Remove 'file' from validated data to prevent overwriting existing file
        $dataToFill = $validated;
        unset($dataToFill['file']);
        
        $regulasi->fill($dataToFill);
        $regulasi->is_active = (bool) ($validated['is_active'] ?? $regulasi->is_active);

        // Only update file if a new file is uploaded
        if ($request->hasFile('file')) {
            // delete old file if stored on public disk
            $this->deletePublicStorageFileIfApplicable($regulasi->file);
            
            $uploadedFile = $request->file('file');
            $extension = $uploadedFile->getClientOriginalExtension();
            
            // Generate custom filename: [Tahun][Jenis Regulasi][Nomor Regulasi]
            $tahun = preg_replace('/[^0-9]/', '', $validated['tahun_peraturan']);
            $jenis = preg_replace('/[^a-zA-Z0-9]/', '', $validated['jenis_peraturan']);
            $nomor = preg_replace('/[^a-zA-Z0-9]/', '', $validated['nomor_peraturan']);
            $customFilename = $tahun . $jenis . $nomor . '.' . $extension;
            
            // Store with custom filename
            $path = $uploadedFile->storeAs('regulasi', $customFilename, 'public');
            $regulasi->file = 'storage/' . $path;
        }

        $regulasi->save();

        Log::info('Regulasi updated', ['id' => $regulasi->id, 'title' => $regulasi->title, 'user_id' => $request->user()?->id]);

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'activity' => 'Updated Regulasi: ' . $regulasi->title,
        ]);

        return redirect()->route('regulasis.index')->with('success', 'Regulasi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Regulasi $regulasi)
    {
        $this->deletePublicStorageFileIfApplicable($regulasi->file);
        $regulasi->delete();
        Log::info('Regulasi deleted', ['id' => $regulasi->id, 'title' => $regulasi->title, 'user_id' => request()->user()?->id]);

        ActivityLog::create([
            'user_id' => request()->user()?->id,
            'activity' => 'Deleted Regulasi: ' . $regulasi->title,
        ]);
        return redirect()->route('regulasis.index')->with('success', 'Regulasi berhasil dihapus');
    }

    private function deletePublicStorageFileIfApplicable(?string $path)
    {
        if (!$path) return;
        // If starts with 'storage/', convert to public disk relative path
        if (str_starts_with($path, 'storage/')) {
            $relative = substr($path, strlen('storage/'));
            Storage::disk('public')->delete($relative);
        }
    }
}
