<?php

namespace App\Http\Controllers;

use App\Models\Bumd;
use App\Models\KondisiKeuangan;
use App\Models\PengurusBumd;
use App\Models\ActivityLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class BumdController extends Controller
{
    public function indexPublic(Request $request)
    {
        Log::info('Accessing public BUMD index', [
            'search' => $request->query('search'),
            'sektor' => $request->query('sektor'),
        ]);

        $search = $request->query('search');
        $sektor = $request->query('sektor');
        $query = Bumd::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%'.$search.'%');
            });
        }
        if ($sektor) {
            $query->where('sektor', 'like', '%'.$sektor.'%');
        }

        $bumds = $query->orderBy('id')->get();

        // Get unique sektors for filter
        $sektors = Bumd::whereNotNull('sektor')
            ->where('sektor', '!=', '')
            ->distinct()
            ->pluck('sektor')
            ->sort()
            ->values();
        $data = [
            'data' => $bumds->transform(function ($b) {
                return [
                    'id' => $b->id,
                    'kode' => $b->kode,
                    'nama_pendek' => $b->nama_pendek,
                    'nama' => $b->nama,
                    'sektor' => $b->sektor,
                    'bidang_usaha' => $b->bidang_usaha,
                    'alamat' => $b->alamat,
                    'telp' => $b->telp,
                    'email' => $b->email,
                    'website' => $b->website,
                    'logo' => $b->logo,
                    'created_at' => $b->created_at?->toDateTimeString(),
                ];
            }),
        ];

        return Inertia::render('Bumds/IndexPublic', [
            'bumds' => $data,
            'sektors' => $sektors,
            'filters' => [
                'search' => $search,
                'sektor' => $sektor,
            ],
        ]);
    }

    public function index(Request $request)
    {
        Log::info('Accessing BUMD index', [
            'user_id' => Auth::id() ?? 'guest',
            'search' => $request->query('search'),
        ]);

        $search = $request->query('search');
        $query = Bumd::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%'.$search.'%')
                    ->orWhere('nama_pendek', 'like', '%'.$search.'%')
                    ->orWhere('kode', 'like', '%'.$search.'%');
            });
        }
        $sortBy = $request->query('sort_by', 'updated_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['nama', 'kode', 'sektor', 'updated_at', 'created_at'];
        if (! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'updated_at';
        }
        $perPage = (int) $request->query('per_page', 10);
        $allowedPerPage = [5, 10, 20, 50];
        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }
        $bumds = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();
        $data = [
            'data' => $bumds->getCollection()->transform(function ($b) {
                return [
                    'id' => $b->id,
                    'kode' => $b->kode,
                    'logo' => $b->logo,
                    'nama' => $b->nama,
                    'sektor' => $b->sektor,
                    'updated_at' => $b->updated_at?->toDateTimeString(),
                    'created_by' => $b->createdBy ? [
                        'id' => $b->createdBy->id,
                        'name' => $b->createdBy->name,
                    ] : null,
                    'updated_by' => $b->updatedBy ? [
                        'id' => $b->updatedBy->id,
                        'name' => $b->updatedBy->name,
                    ] : null,
                ];
            }),
            'links' => [
                'first' => $bumds->url(1),
                'last' => $bumds->url($bumds->lastPage()),
                'prev' => $bumds->previousPageUrl(),
                'next' => $bumds->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $bumds->currentPage(),
                'last_page' => $bumds->lastPage(),
                'per_page' => $bumds->perPage(),
                'total' => $bumds->total(),
            ],
        ];

        return Inertia::render('Bumds/Index', [
            'bumds' => $data,
            'filters' => [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Bumds/Create');
    }

    /**
     * Store - VERSI DENGAN UPLOAD FILE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:bumd,kode'],
            'nama_pendek' => ['required', 'string', 'max:255', 'unique:bumd,nama_pendek'],
            'nama' => ['required', 'string', 'max:255', 'unique:bumd,nama'],
            'kategory' => ['required', 'string', 'max:255'],
            'sektor' => ['required', 'string', 'max:255'],
            'bidang_usaha' => ['required', 'string', 'max:255'],
            'hasil_usaha' => ['nullable', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:255'],
            'hotline' => ['nullable', 'string', 'max:255'],
            'telp' => ['nullable', 'string', 'max:255'],
            'fax' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'visi' => ['nullable', 'string'],
            'misi' => ['nullable', 'string'],
            'tujuan' => ['nullable', 'string'],
            'logo' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (is_file($value)) {
                        $validator = validator(
                            ['logo' => $value],
                            ['logo' => 'image|mimes:jpg,jpeg,png,svg|max:2048']
                        );
                        if ($validator->fails()) {
                            $fail($validator->errors()->first('logo'));
                        }
                    } elseif (! is_string($value)) {
                        $fail('Logo tidak valid.');
                    }
                },
            ],
            'pengurus' => ['nullable', 'string'],
            'kondisi_keuangan' => ['nullable', 'string'],
        ], [
            // Kode
            'kode.required' => 'Kode BUMD wajib diisi.',
            'kode.string' => 'Kode BUMD harus berupa teks.',
            'kode.max' => 'Kode BUMD tidak boleh lebih dari 10 karakter.',
            'kode.unique' => 'Kode BUMD sudah digunakan. Silakan gunakan kode yang berbeda.',
            
            // Nama Pendek
            'nama_pendek.required' => 'Nama pendek BUMD wajib diisi.',
            'nama_pendek.string' => 'Nama pendek BUMD harus berupa teks.',
            'nama_pendek.max' => 'Nama pendek BUMD tidak boleh lebih dari 255 karakter.',
            'nama_pendek.unique' => 'Nama pendek BUMD sudah digunakan. Silakan gunakan nama yang berbeda.',
            
            // Nama
            'nama.required' => 'Nama lengkap BUMD wajib diisi.',
            'nama.string' => 'Nama lengkap BUMD harus berupa teks.',
            'nama.max' => 'Nama lengkap BUMD tidak boleh lebih dari 255 karakter.',
            'nama.unique' => 'Nama BUMD sudah terdaftar. Silakan gunakan nama yang berbeda.',
            
            // Kategori
            'kategory.required' => 'Kategori BUMD wajib dipilih.',
            'kategory.string' => 'Kategori BUMD harus berupa teks.',
            'kategory.max' => 'Kategori BUMD tidak boleh lebih dari 255 karakter.',
            
            // Sektor
            'sektor.required' => 'Sektor usaha BUMD wajib dipilih.',
            'sektor.string' => 'Sektor usaha harus berupa teks.',
            'sektor.max' => 'Sektor usaha tidak boleh lebih dari 255 karakter.',
            
            // Bidang Usaha
            'bidang_usaha.required' => 'Bidang usaha BUMD wajib diisi.',
            'bidang_usaha.string' => 'Bidang usaha harus berupa teks.',
            'bidang_usaha.max' => 'Bidang usaha tidak boleh lebih dari 255 karakter.',
            
            // Hasil Usaha
            'hasil_usaha.string' => 'Hasil usaha harus berupa teks.',
            'hasil_usaha.max' => 'Hasil usaha tidak boleh lebih dari 255 karakter.',
            
            // Alamat
            'alamat.required' => 'Alamat BUMD wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max' => 'Alamat tidak boleh lebih dari 255 karakter.',
            
            // Hotline
            'hotline.string' => 'Nomor hotline harus berupa teks.',
            'hotline.max' => 'Nomor hotline tidak boleh lebih dari 255 karakter.',
            
            // Telepon
            'telp.string' => 'Nomor telepon harus berupa teks.',
            'telp.max' => 'Nomor telepon tidak boleh lebih dari 255 karakter.',
            
            // Fax
            'fax.string' => 'Nomor fax harus berupa teks.',
            'fax.max' => 'Nomor fax tidak boleh lebih dari 255 karakter.',
            
            // Email
            'email.email' => 'Format email tidak valid. Contoh: nama@domain.com',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            
            // Website
            'website.url' => 'Format website tidak valid. Harus dimulai dengan http:// atau https://',
            'website.max' => 'URL website tidak boleh lebih dari 255 karakter.',
            
            // Visi, Misi, Tujuan
            'visi.string' => 'Visi harus berupa teks.',
            'misi.string' => 'Misi harus berupa teks.',
            'tujuan.string' => 'Tujuan harus berupa teks.',
            
            // Pengurus & Kondisi Keuangan
            'pengurus.string' => 'Data pengurus tidak valid.',
            'kondisi_keuangan.string' => 'Data kondisi keuangan tidak valid.',
        ]);

        $bumdData = $validated;
        unset($bumdData['pengurus'], $bumdData['kondisi_keuangan']);

        if ($request->hasFile('logo')) {
            $storedPath = $request->file('logo')->store('bumd', 'public');
            $bumdData['logo'] = Storage::url($storedPath);
        } else {
            $bumdData['logo'] = $request->input('logo');
        }

        $bumd = Bumd::create($bumdData);

        if ($request->has('pengurus')) {
            $pengurusData = json_decode($request->pengurus, true);
            if (is_array($pengurusData) && ! empty($pengurusData)) {
                $bumd->pengurusBumds()->createMany($pengurusData);
            }
        }

        if ($request->has('kondisi_keuangan')) {
            $keuanganData = json_decode($request->kondisi_keuangan, true);
            if (is_array($keuanganData) && ! empty($keuanganData)) {
                $bumd->kondisiKeuangan()->createMany($keuanganData);
            }
        }

        // Activity Log
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Created BUMD: ' . $bumd->nama,
            'description' => 'Membuat BUMD baru dengan kode ' . $bumd->kode . ' (' . $bumd->nama_pendek . ')',
            'subject_type' => 'App\Models\Bumd',
            'subject_id' => $bumd->id,
            'properties' => json_encode([
                'kode' => $bumd->kode,
                'nama' => $bumd->nama,
                'nama_pendek' => $bumd->nama_pendek,
            ]),
        ]);

        Log::info('BUMD created successfully', [
            'user_id' => Auth::id(),
            'bumd_id' => $bumd->id,
            'kode' => $bumd->kode,
            'nama' => $bumd->nama,
        ]);

        return redirect()->route('bumds.index')->with('status', 'BUMD created.');
    }

    public function show(Bumd $bumd)
    {
        Log::info('Viewing BUMD details', [
            'user_id' => Auth::id() ?? 'guest',
            'bumd_id' => $bumd->id,
            'kode' => $bumd->kode,
        ]);

        // Ambil data pengurus dan kondisi keuangan
        $pengurusList = PengurusBumd::where('bumd_id', $bumd->id)->get();
        $KeuanganList = KondisiKeuangan::where('bumd_id', $bumd->id)->get();

        return Inertia::render('Bumds/Show', [
            'bumd' => [
                'id' => $bumd->id,
                'kode' => $bumd->kode,
                'nama_pendek' => $bumd->nama_pendek,
                'nama' => $bumd->nama,
                'kategory' => $bumd->kategory,
                'sektor' => $bumd->sektor,
                'bidang_usaha' => $bumd->bidang_usaha,
                'hasil_usaha' => $bumd->hasil_usaha,
                'akta_pendirian' => $bumd->akta_pendirian,
                'akta_perubahan' => $bumd->akta_perubahan,
                'dasar_hukum' => $bumd->dasar_hukum,
                'nilai_saham' => $bumd->nilai_saham,
                'alamat' => $bumd->alamat,
                'hotline' => $bumd->hotline,
                'telp' => $bumd->telp,
                'fax' => $bumd->fax,
                'email' => $bumd->email,
                'website' => $bumd->website,
                'visi' => $bumd->visi,
                'misi' => $bumd->misi,
                'tujuan' => $bumd->tujuan,
                'logo' => $bumd->logo,
                'kondisi_keuangans' => $KeuanganList->map(function ($q) {
                    return [
                        'id' => $q->id,
                        'no_akun' => $q->no_akun,
                        'nama_akun' => $q->nama_akun,
                        'tahun_2020' => $q->tahun_2020,
                        'tahun_2021' => $q->tahun_2021,
                        'tahun_2022' => $q->tahun_2022,
                        'tahun_2023' => $q->tahun_2023,
                        'tahun_2024' => $q->tahun_2024,
                    ];
                })->values(),
                'pengurus' => $pengurusList->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'nama' => $p->nama,
                        'jabatan' => $p->jabatan,
                        'grup' => $p->grup,
                        'urutan' => $p->urutan,
                    ];
                })->values(),
                'created_at' => $bumd->created_at?->toDateTimeString(),
                'updated_at' => $bumd->updated_at?->toDateTimeString(),
                'created_by' => $bumd->createdBy ? [
                    'id' => $bumd->createdBy->id,
                    'name' => $bumd->createdBy->name,
                    'email' => $bumd->createdBy->email,
                ] : null,
                'updated_by' => $bumd->updatedBy ? [
                    'id' => $bumd->updatedBy->id,
                    'name' => $bumd->updatedBy->name,
                    'email' => $bumd->updatedBy->email,
                ] : null,
            ],
        ]);
    }

    public function showPublic(Bumd $bumd)
    {
        Log::info('Viewing public BUMD details', [
            'bumd_id' => $bumd->id,
            'kode' => $bumd->kode,
        ]);

        // ambil collection pengurus dan first item
        $pengurusList = PengurusBumd::where('bumd_id', $bumd->id)->get();
        $firstPengurus = $pengurusList->first();
        $KeuanganList = KondisiKeuangan::where('bumd_id', $bumd->id)->get();

        return Inertia::render('Bumds/ShowPublic', [
            'bumd' => [
                'id' => $bumd->id,
                'kode' => $bumd->kode,
                'nama_pendek' => $bumd->nama_pendek,
                'nama' => $bumd->nama,
                'kategory' => $bumd->kategory,
                'sektor' => $bumd->sektor,
                'bidang_usaha' => $bumd->bidang_usaha,
                'hasil_usaha' => $bumd->hasil_usaha,
                'alamat' => $bumd->alamat,
                'hotline' => $bumd->hotline,
                'telp' => $bumd->telp,
                'fax' => $bumd->fax,
                'email' => $bumd->email,
                'website' => $bumd->website,
                'visi' => $bumd->visi,
                'misi' => $bumd->misi,
                'tujuan' => $bumd->tujuan,
                'logo' => $bumd->logo,
                'namapengurus' => $firstPengurus?->nama,
                'jabatan' => $firstPengurus?->jabatan,
                'grup' => $firstPengurus?->grup,
                'kondisi_keuangans' => $KeuanganList->map(function ( $q) {
                    return [
                        'id' => $q->id,
                        'no_akun' => $q->no_akun,
                        'nama_akun' => $q->nama_akun,
                        'tahun_2020' => $q->tahun_2020,
                        'tahun_2021' => $q->tahun_2021,
                        'tahun_2022' => $q->tahun_2022,
                        'tahun_2023' => $q->tahun_2023,
                        'tahun_2024' => $q->tahun_2024,
                    ];
                })->values(),
                'pengurus' => $pengurusList->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'nama' => $p->nama,
                        'jabatan' => $p->jabatan,
                        'grup' => $p->grup,
                    ];
                })->values(),
                'created_at' => $bumd->created_at?->toDateTimeString(),
                'updated_at' => $bumd->updated_at?->toDateTimeString(),
            ],
        ]);
    }

    public function edit(Bumd $bumd)
    {
        Log::info('Editing BUMD', [
            'user_id' => Auth::id() ?? 'guest',
            'bumd_id' => $bumd->id,
            'kode' => $bumd->kode,
        ]);

        $pengurusList = PengurusBumd::where('bumd_id', $bumd->id)->get();
        $firstPengurus = $pengurusList->first();
        $KeuanganList = KondisiKeuangan::where('bumd_id', $bumd->id)->get();
        return Inertia::render('Bumds/Edit', [
            'bumd' => [
                'id' => $bumd->id,
                'kode' => $bumd->kode,
                'nama_pendek' => $bumd->nama_pendek,
                'nama' => $bumd->nama,
                'kategory' => $bumd->kategory,
                'sektor' => $bumd->sektor,
                'bidang_usaha' => $bumd->bidang_usaha,
                'hasil_usaha' => $bumd->hasil_usaha,
                'alamat' => $bumd->alamat,
                'hotline' => $bumd->hotline,
                'telp' => $bumd->telp,
                'fax' => $bumd->fax,
                'email' => $bumd->email,
                'website' => $bumd->website,
                'visi' => $bumd->visi,
                'misi' => $bumd->misi,
                'tujuan' => $bumd->tujuan,
                'logo' => $bumd->logo,
                'kondisi_keuangans' => $KeuanganList->map(function ( $q) {
                    return [
                        'id' => $q->id,
                        'no_akun' => $q->no_akun,
                        'nama_akun' => $q->nama_akun,
                        'tahun_2020' => $q->tahun_2020,
                        'tahun_2021' => $q->tahun_2021,
                        'tahun_2022' => $q->tahun_2022,
                        'tahun_2023' => $q->tahun_2023,
                        'tahun_2024' => $q->tahun_2024,
                    ];
                })->values(),
                'pengurus' => $pengurusList->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'nama' => $p->nama,
                        'jabatan' => $p->jabatan,
                        'grup' => $p->grup,
                    ];
                })->values(),
                'created_by' => $bumd->createdBy ? [
                    'id' => $bumd->createdBy->id,
                    'name' => $bumd->createdBy->name,
                    'email' => $bumd->createdBy->email,
                ] : null,
                'updated_by' => $bumd->updatedBy ? [
                    'id' => $bumd->updatedBy->id,
                    'name' => $bumd->updatedBy->name,
                    'email' => $bumd->updatedBy->email,
                ] : null,
            ],
        ]);
    }

    /**
     * Update - VERSI DENGAN UPLOAD FILE
     */
    public function update(Request $request, Bumd $bumd)
    {
        try {
            Log::info('Updating BUMD', [
                'user_id' => Auth::id() ?? 'guest',
                'bumd_id' => $bumd->id,
                'kode' => $request->input('kode'),
            ]);

            // Build conditional logo validation rules explicitly to avoid pipe-string misinterpretation
            $logoRules = ['nullable'];
            if ($request->hasFile('logo')) {
                $logoRules[] = 'image';
                $logoRules[] = 'mimes:jpg,jpeg,png,svg';
                $logoRules[] = 'max:2048';
            } else {
                $logoRules[] = 'string';
                $logoRules[] = 'max:255';
            }

            $validated = $request->validate([
                'kode' => ['required', 'string', 'max:10', 'unique:bumd,kode,'.$bumd->id],
                'nama_pendek' => ['required', 'string', 'max:255', 'unique:bumd,nama_pendek,'.$bumd->id],
                'nama' => ['required', 'string', 'max:255', 'unique:bumd,nama,'.$bumd->id],
                'kategory' => ['nullable', 'string', 'max:255'],
                'sektor' => ['nullable', 'string', 'max:255'],
                'bidang_usaha' => ['nullable', 'string', 'max:255'],
                'hasil_usaha' => ['nullable', 'string', 'max:255'],
                'alamat' => ['nullable', 'string', 'max:255'],
                'hotline' => ['nullable', 'string', 'max:255'],
                'telp' => ['nullable', 'string', 'max:255'],
                'fax' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'website' => ['nullable', 'max:255'],
                'visi' => ['nullable', 'string'],
                'misi' => ['nullable', 'string'],
                'tujuan' => ['nullable', 'string'],
                'logo' => $logoRules,
                // BARU: Aturan validasi untuk data JSON
                'pengurus' => ['nullable', 'string'],
                'kondisi_keuangan' => ['nullable', 'string'],
            ], [
                // Kode
                'kode.required' => 'Kode BUMD wajib diisi.',
                'kode.string' => 'Kode BUMD harus berupa teks.',
                'kode.max' => 'Kode BUMD tidak boleh lebih dari 10 karakter.',
                'kode.unique' => 'Kode BUMD sudah digunakan oleh BUMD lain. Silakan gunakan kode yang berbeda.',
                
                // Nama Pendek
                'nama_pendek.required' => 'Nama pendek BUMD wajib diisi.',
                'nama_pendek.string' => 'Nama pendek BUMD harus berupa teks.',
                'nama_pendek.max' => 'Nama pendek BUMD tidak boleh lebih dari 255 karakter.',
                'nama_pendek.unique' => 'Nama pendek BUMD sudah digunakan oleh BUMD lain. Silakan gunakan nama yang berbeda.',
                
                // Nama
                'nama.required' => 'Nama lengkap BUMD wajib diisi.',
                'nama.string' => 'Nama lengkap BUMD harus berupa teks.',
                'nama.max' => 'Nama lengkap BUMD tidak boleh lebih dari 255 karakter.',
                'nama.unique' => 'Nama BUMD sudah terdaftar untuk BUMD lain. Silakan gunakan nama yang berbeda.',
                
                // Kategori
                'kategory.string' => 'Kategori BUMD harus berupa teks.',
                'kategory.max' => 'Kategori BUMD tidak boleh lebih dari 255 karakter.',
                
                // Sektor
                'sektor.string' => 'Sektor usaha harus berupa teks.',
                'sektor.max' => 'Sektor usaha tidak boleh lebih dari 255 karakter.',
                
                // Bidang Usaha
                'bidang_usaha.string' => 'Bidang usaha harus berupa teks.',
                'bidang_usaha.max' => 'Bidang usaha tidak boleh lebih dari 255 karakter.',
                
                // Hasil Usaha
                'hasil_usaha.string' => 'Hasil usaha harus berupa teks.',
                'hasil_usaha.max' => 'Hasil usaha tidak boleh lebih dari 255 karakter.',
                
                // Alamat
                'alamat.string' => 'Alamat harus berupa teks.',
                'alamat.max' => 'Alamat tidak boleh lebih dari 255 karakter.',
                
                // Hotline
                'hotline.string' => 'Nomor hotline harus berupa teks.',
                'hotline.max' => 'Nomor hotline tidak boleh lebih dari 255 karakter.',
                
                // Telepon
                'telp.string' => 'Nomor telepon harus berupa teks.',
                'telp.max' => 'Nomor telepon tidak boleh lebih dari 255 karakter.',
                
                // Fax
                'fax.string' => 'Nomor fax harus berupa teks.',
                'fax.max' => 'Nomor fax tidak boleh lebih dari 255 karakter.',
                
                // Email
                'email.email' => 'Format email tidak valid. Contoh: nama@domain.com',
                'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
                
                // Website
                'website.url' => 'Format website tidak valid. Harus dimulai dengan http:// atau https://',
                'website.max' => 'URL website tidak boleh lebih dari 255 karakter.',
                
                // Visi, Misi, Tujuan
                'visi.string' => 'Visi harus berupa teks.',
                'misi.string' => 'Misi harus berupa teks.',
                'tujuan.string' => 'Tujuan harus berupa teks.',
                
                // Logo
                'logo.image' => 'File logo harus berupa gambar.',
                'logo.mimes' => 'Logo harus berformat JPG, JPEG, PNG, atau SVG.',
                'logo.max' => 'Ukuran file logo tidak boleh lebih dari 2MB.',
                'logo.string' => 'Logo harus berupa teks atau URL yang valid.',
                
                // Pengurus & Kondisi Keuangan
                'pengurus.string' => 'Data pengurus tidak valid.',
                'kondisi_keuangan.string' => 'Data kondisi keuangan tidak valid.',
            ]);

            $oldLogoPath = $bumd->logo;

            // MODIFIKASI: Pisahkan data BUMD utama dari data relasi
            $bumdData = $validated;
            unset($bumdData['pengurus'], $bumdData['kondisi_keuangan']);

            if ($request->hasFile('logo')) {
                // New logo file uploaded
                $storedPath = $request->file('logo')->store('bumd', 'public');
                $bumdData['logo'] = Storage::url($storedPath);

                // Delete old logo file
                if ($oldLogoPath && Str::startsWith($oldLogoPath, '/storage/')) {
                    $oldRel = Str::replaceFirst('/storage/', '', $oldLogoPath);
                    if (Storage::disk('public')->exists($oldRel)) {
                        Storage::disk('public')->delete($oldRel);
                    }
                } elseif ($oldLogoPath && file_exists(public_path($oldLogoPath)) && ! Str::contains($oldLogoPath, 'bumd-logos')) {
                    @unlink(public_path($oldLogoPath));
                }

            } elseif (array_key_exists('logo', $bumdData) && ($bumdData['logo'] === null || $bumdData['logo'] === '')) {
                // Logo explicitly cleared (null or empty string)
                $bumdData['logo'] = null;
                
                // Delete old logo file
                if ($oldLogoPath && Str::startsWith($oldLogoPath, '/storage/')) {
                    $oldRel = Str::replaceFirst('/storage/', '', $oldLogoPath);
                    if (Storage::disk('public')->exists($oldRel)) {
                        Storage::disk('public')->delete($oldRel);
                    }
                } elseif ($oldLogoPath && file_exists(public_path($oldLogoPath)) && ! Str::contains($oldLogoPath, 'bumd-logos')) {
                    @unlink(public_path($oldLogoPath));
                }
            } else {
                // No change to logo - preserve existing value
                // This prevents logo from being removed when updating other fields like pengurus or kondisi_keuangan
                unset($bumdData['logo']);
            }

            $bumd->update($bumdData); // MODIFIKASI

            // --- BARU: Proses data relasi setelah BUMD di-update ---

            // 1. Proses Pengurus (Update, Create, Delete)
            if ($request->has('pengurus')) {
                $pengurusData = json_decode($request->pengurus, true);
                if (is_array($pengurusData)) {
                    $incomingIds = collect($pengurusData)->pluck('id')->filter();

                    // 1. Hapus pengurus yang tidak ada di request baru
                    $bumd->pengurusBumds()->whereNotIn('id', $incomingIds)->delete();

                    // 2. Update atau Buat (UpdateOrCreate)
                    foreach ($pengurusData as $data) {
                        $bumd->pengurusBumds()->updateOrCreate(
                            ['id' => $data['id'] ?? null], // Cari berdasarkan ID (atau buat jika ID null)
                            $data // Data untuk di-update/create
                        );
                    }
                }
            }

            // 2. Proses Kondisi Keuangan (Hanya Update/Create, asumsi tidak ada delete)
            if ($request->has('kondisi_keuangan')) {
                $keuanganData = json_decode($request->kondisi_keuangan, true);
                if (is_array($keuanganData)) {
                    foreach ($keuanganData as $data) {
                        $bumd->kondisiKeuangan()->updateOrCreate(
                            // Cari berdasarkan ID (jika ada) ATAU no_akun
                            ['id' => $data['id'] ?? null, 'no_akun' => $data['no_akun']],
                            $data // Data untuk di-update/create
                        );
                    }
                }
            }

            // --- AKHIR BLOK BARU ---

            // Activity Log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Updated BUMD: ' . $bumd->nama,
                'description' => 'Memperbarui informasi BUMD dengan kode ' . $bumd->kode . ' (' . $bumd->nama_pendek . ')',
                'subject_type' => 'App\Models\Bumd',
                'subject_id' => $bumd->id,
                'properties' => json_encode([
                    'kode' => $bumd->kode,
                    'nama' => $bumd->nama,
                    'nama_pendek' => $bumd->nama_pendek,
                    'changes' => $bumd->getChanges(),
                ]),
            ]);

            Log::info('BUMD updated successfully', [
                'bumd_id' => $bumd->id,
                'kode' => $bumd->kode,
            ]);
            // dd($request);

            return redirect()->route('bumds.index')->with('status', 'BUMD updated.');

        } catch (ValidationException $e) {
            // ... (Blok catch Anda sudah benar) ...
            Log::warning('Validation failed when updating BUMD', [
                'bumd_id' => $bumd->id,
                'errors' => $e->errors(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            // ... (Blok catch Anda sudah benar) ...
            Log::error('Error updating BUMD', [
                'bumd_id' => $bumd->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui BUMD: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Destroy - DENGAN LOGIKA HAPUS FILE
     */
    public function destroy(Bumd $bumd) // <-- PERBAIKAN (sebelumnya publicMfnction)
    {
        $logoPath = $bumd->logo;
        $bumdData = [
            'kode' => $bumd->kode,
            'nama' => $bumd->nama,
            'nama_pendek' => $bumd->nama_pendek,
        ];

        if ($logoPath) {
            if (Str::startsWith($logoPath, '/storage/')) {
                $oldRel = Str::replaceFirst('/storage/', '', $logoPath);
                if (Storage::disk('public')->exists($oldRel)) {
                    Storage::disk('public')->delete($oldRel);
                }
            } elseif (file_exists(public_path($logoPath)) && ! Str::contains($logoPath, 'bumd-logos')) {
                @unlink(public_path($logoPath));
            }
        }

        $bumd->delete();

        // Activity Log
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Deleted BUMD: ' . $bumdData['nama'],
            'description' => 'Menghapus BUMD dengan kode ' . $bumdData['kode'] . ' (' . $bumdData['nama_pendek'] . ')',
            'subject_type' => 'App\Models\Bumd',
            'subject_id' => $bumd->id,
            'properties' => json_encode($bumdData),
        ]);

        Log::info('BUMD deleted successfully', [
            'user_id' => Auth::id(),
            'bumd_id' => $bumd->id,
            'kode' => $bumdData['kode'],
            'nama' => $bumdData['nama'],
        ]);

        return redirect()->route('bumds.index')->with('status', 'BUMD deleted.');
    }

    public function daftarKepemilikan()
    {
        return Inertia::render('Bumds/DaftarKepemilikan');
    }
}
