<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class PengumumanController extends Controller
{
    /**
     * Handle image upload: convert to WebP + resize
     */
    private function handleImage($file, $folder = 'pengumuman', $maxWidth = 1200)
    {
        $originalExtension = strtolower($file->getClientOriginalExtension());

        if ($originalExtension === 'webp') {
            $path = $file->store($folder, 'public');
            return 'storage/' . $path;
        }

        $image = null;
        switch ($originalExtension) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($file);
                break;
            case 'png':
                $image = imagecreatefrompng($file);
                imagealphablending($image, false);
                imagesavealpha($image, true);
                break;
            case 'gif':
                $image = imagecreatefromgif($file);
                break;
            default:
                return 'storage/' . $file->store($folder, 'public');
        }

        $width = imagesx($image);
        $height = imagesy($image);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) (($height / $width) * $newWidth);
            $resized = imagecreatetruecolor($newWidth, $newHeight);

            if ($originalExtension === 'png') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                imagefill($resized, 0, 0, $transparent);
            }

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        $filename = uniqid() . '.webp';
        $path = $folder . '/' . $filename;
        imagewebp($image, storage_path('app/public/' . $path), 85);
        imagedestroy($image);

        return 'storage/' . $path;
    }

    /**
     * Index admin
     */
    public function index(Request $request)
    {
        $query = Pengumuman::with(['creator', 'updater']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('konten', 'like', "%{$search}%")
                    ->orWhere('nomor_pengumuman', 'like', "%{$search}%");
            });
        }

        if ($tipe = $request->query('tipe')) {
            $query->where('tipe', $tipe);
        }

        if ($status = $request->query('status')) {
            if ($status === 'aktif')
                $query->where('is_aktif', true);
            if ($status === 'nonaktif')
                $query->where('is_aktif', false);
        }

        $sortBy = $request->query('sort_by', 'updated_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage = in_array((int) $request->query('per_page', 10), [5, 10, 20, 50]) ? (int) $request->query('per_page', 10) : 10;

        $pengumuman = $query->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

        $data = [
            'data' => $pengumuman->getCollection()->transform(function ($p) {
                return [
                    'id' => $p->id,
                    'judul' => $p->judul,
                    'slug' => $p->slug,
                    'tipe' => $p->tipe,
                    'nomor_pengumuman' => $p->nomor_pengumuman,
                    'tanggal_terbit' => $p->tanggal_terbit?->format('Y-m-d'),
                    'tanggal_berakhir' => $p->tanggal_berakhir?->format('Y-m-d'),
                    'is_aktif' => (bool) $p->is_aktif,
                    'is_penting' => (bool) $p->is_penting,
                    'gambar' => $p->image_url,
                    'dokumen' => $p->dokumen ? asset($p->dokumen) : null,
                    'created_by' => $p->creator?->only('id', 'name'),
                    'updated_by' => $p->updater?->only('id', 'name'),
                    'updated_at' => $p->updated_at?->toDateTimeString(),
                ];
            }),
            'links' => [
                'first' => $pengumuman->url(1),
                'last' => $pengumuman->url($pengumuman->lastPage()),
                'prev' => $pengumuman->previousPageUrl(),
                'next' => $pengumuman->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $pengumuman->currentPage(),
                'last_page' => $pengumuman->lastPage(),
                'per_page' => $pengumuman->perPage(),
                'total' => $pengumuman->total(),
            ]
        ];

        return Inertia::render('Pengumuman/Index', [
            'pengumuman' => $data,
            'tipeOptions' => ['pengumuman', 'pemberitahuan', 'undangan', 'lowongan', 'laporan', 'lainnya'],
            'filters' => $request->only(['search', 'tipe', 'status', 'sort_by', 'sort_dir', 'per_page'])
        ]);
    }

    /**
     * Show form create
     */
    public function create()
    {
        return Inertia::render('Pengumuman/Form', [
            'formTitle' => 'Tambah Pengumuman',
            'submitUrl' => route('admin.pengumuman.store'),
            'method' => 'post',
            'tipeOptions' => ['pengumuman', 'pemberitahuan', 'undangan', 'lowongan', 'laporan', 'lainnya'],
            'pengumuman' => (object) [],
        ]);
    }

    /**
     * Store pengumuman
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:pengumuman',
                'konten' => 'required|string',
                'excerpt' => 'nullable|string|max:500',
                'tipe' => 'required|string|in:pengumuman,pemberitahuan,undangan,lowongan,laporan,lainnya',
                'nomor_pengumuman' => 'nullable|string|max:100',
                'tanggal_terbit' => 'required|date',
                'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_terbit',
                'is_penting' => 'nullable',
                'is_aktif' => 'nullable',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'dokumen' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            ]);

            if (empty($validated['slug'])) {
                $slugBase = Str::slug($validated['judul']);
                $slug = $slugBase;
                $i = 1;
                while (Pengumuman::where('slug', $slug)->exists())
                    $slug = $slugBase . '-' . $i++;
                $validated['slug'] = $slug;
            }

            if ($request->hasFile('gambar'))
                $validated['gambar'] = $this->handleImage($request->file('gambar'));

            if ($request->hasFile('dokumen')) {
                $path = $request->file('dokumen')->store('pengumuman/dokumen', 'public');
                $validated['dokumen'] = 'storage/' . $path;
            }

            $validated['is_penting'] = (int) $request->input('is_penting', 0);
            $validated['is_aktif'] = (int) $request->input('is_aktif', 1);
            $validated['created_by'] = auth()->id();

            $pengumuman = Pengumuman::create($validated);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'Created pengumuman: ' . $pengumuman->judul,
            ]);

            return redirect()->route('admin.pengumuman.index')->with('status', 'Pengumuman berhasil dibuat.');
        } catch (Exception $e) {
            Log::error('Store pengumuman error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Gagal menyimpan pengumuman')->withInput();
        }
    }

    /**
     * Edit form
     */
    public function edit(Pengumuman $pengumuman)
    {
        return Inertia::render('Pengumuman/Edit', [
            'pengumuman' => $pengumuman,
            'tipeOptions' => ['pengumuman', 'pemberitahuan', 'undangan', 'lowongan', 'laporan', 'lainnya']
        ]);
    }

    /**
     * Update pengumuman
     */
    public function update(Request $request, Pengumuman $pengumuman)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:pengumuman,slug,' . $pengumuman->id,
                'konten' => 'required|string',
                'excerpt' => 'nullable|string|max:500',
                'tipe' => 'required|string|in:pengumuman,pemberitahuan,undangan,lowongan,laporan,lainnya',
                'nomor_pengumuman' => 'nullable|string|max:100',
                'tanggal_terbit' => 'required|date',
                'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_terbit',
                'is_penting' => 'nullable',
                'is_aktif' => 'nullable',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'dokumen' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            ]);

            if (empty($validated['slug'])) {
                $slugBase = Str::slug($validated['judul']);
                $slug = $slugBase;
                $i = 1;
                while (Pengumuman::where('slug', $slug)->where('id', '!=', $pengumuman->id)->exists())
                    $slug = $slugBase . '-' . $i++;
                $validated['slug'] = $slug;
            }

            if ($request->hasFile('gambar')) {
                if ($pengumuman->gambar && Storage::exists(str_replace('storage/', 'public/', $pengumuman->gambar))) {
                    Storage::delete(str_replace('storage/', 'public/', $pengumuman->gambar));
                }
                $validated['gambar'] = $this->handleImage($request->file('gambar'));
            }

            if ($request->hasFile('dokumen')) {
                if ($pengumuman->dokumen && Storage::exists(str_replace('storage/', 'public/', $pengumuman->dokumen))) {
                    Storage::delete(str_replace('storage/', 'public/', $pengumuman->dokumen));
                }
                $path = $request->file('dokumen')->store('pengumuman/dokumen', 'public');
                $validated['dokumen'] = 'storage/' . $path;
            } elseif ($request->has('remove_dokumen')) {
                if ($pengumuman->dokumen && Storage::exists(str_replace('storage/', 'public/', $pengumuman->dokumen))) {
                    Storage::delete(str_replace('storage/', 'public/', $pengumuman->dokumen));
                }
                $validated['dokumen'] = null;
            }

            $validated['is_penting'] = (int) $request->input('is_penting', 0);
            $validated['is_aktif'] = (int) $request->input('is_aktif', 1);
            $validated['updated_by'] = auth()->id();

            $pengumuman->update($validated);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'Updated pengumuman: ' . $pengumuman->judul
            ]);

            return redirect()->route('admin.pengumuman.index')->with('status', 'Pengumuman berhasil diperbarui.');

        } catch (Exception $e) {
            Log::error('Update pengumuman error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Gagal memperbarui pengumuman')->withInput();
        }
    }

    /**
     * Destroy pengumuman
     */
    public function destroy(Pengumuman $pengumuman)
    {
        try {
            if ($pengumuman->gambar && Storage::exists(str_replace('storage/', 'public/', $pengumuman->gambar))) {
                Storage::delete(str_replace('storage/', 'public/', $pengumuman->gambar));
            }
            if ($pengumuman->dokumen && Storage::exists(str_replace('storage/', 'public/', $pengumuman->dokumen))) {
                Storage::delete(str_replace('storage/', 'public/', $pengumuman->dokumen));
            }

            $judul = $pengumuman->judul;
            $pengumuman->delete();

            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'Deleted pengumuman: ' . $judul
            ]);

            return redirect()->route('admin.pengumuman.index')->with('status', 'Pengumuman berhasil dihapus.');

        } catch (Exception $e) {
            Log::error('Delete pengumuman error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Gagal menghapus pengumuman.');
        }
    }

    /**
     * Public index
     */
    public function indexPublic(Request $request)
    {
        $query = Pengumuman::aktif()->latest('tanggal_terbit');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('konten', 'like', "%{$search}%")
                    ->orWhere('nomor_pengumuman', 'like', "%{$search}%");
            });
        }

        if ($tipe = $request->query('tipe'))
            $query->where('tipe', $tipe);
        if ($tahun = $request->query('tahun'))
            $query->whereYear('tanggal_terbit', $tahun);

        $pengumuman = $query->paginate(12)->withQueryString();

        $random_pengumuman = Pengumuman::aktif()->inRandomOrder()->take(5)->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'judul' => $p->judul,
                'slug' => $p->slug,
                'tanggal_terbit' => $p->tanggal_terbit?->format('Y-m-d')
            ];
        });

        return Inertia::render('Pengumuman/IndexPublic', [
            'pengumuman' => $pengumuman,
            'random_pengumuman' => $random_pengumuman,
            'filters' => $request->only(['search', 'tipe', 'tahun']),
        ]);
    }

    /**
     * Public show
     */
    public function showPublic(Pengumuman $pengumuman)
    {
        if (!$pengumuman->is_aktif || $pengumuman->tanggal_terbit > now())
            abort(404);

        $related = Pengumuman::aktif()->where('id', '!=', $pengumuman->id)->where('tipe', $pengumuman->tipe)->latest('tanggal_terbit')->take(4)->get();

        return Inertia::render('Pengumuman/ShowPublic', [
            'pengumuman' => $pengumuman,
            'relatedPengumuman' => $related
        ]);
    }
}
