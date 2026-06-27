<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\PermohonanInformasi;
use App\Models\PengajuanKeberatan;

class PPIDController extends Controller
{
    public function profil()
    {
        return Inertia::render('PPID/InformasiPPID/Profil');
    }

    public function visimisi()
    {
        return Inertia::render('PPID/InformasiPPID/VisiMisiPPID');
    }

    public function strukturorganisasi()
    {
        return Inertia::render('PPID/InformasiPPID/StrukturPPID');
    }

    public function tugasfungsi()
    {
        return Inertia::render('PPID/InformasiPPID/TugasFungsiPPID');
    }

    public function dasarhukum()
    {
        return Inertia::render('PPID/InformasiPPID/DasarHukumPPID');
    }

    public function informasiberkala()
    {
        return Inertia::render('PPID/InformasiPublik/InformasiBerkala');
    }

    public function informasieseluruhwaktu()
    {
        return Inertia::render('PPID/InformasiPublik/InformasiSetiapSaat');
    }

    public function informasiesertamerta()
    {
        return Inertia::render('PPID/InformasiPublik/InformasiSertaMerta');
    }

    public function maklumatlayanan()
    {
        return Inertia::render('PPID/StandarLayanan/MaklumatLayanan');
    }

    public function permohonaninformasi()
    {
        return Inertia::render('PPID/StandarLayanan/PermohonanInformasi');
    }

    public function permohonanInformasiForm()
    {
        return Inertia::render('PPID/StandarLayanan/PermohonanInformasiForm');
    }

    public function storePermohonanInformasi(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:1000',
            'no_telepon_email' => 'required|string|max:255',
            'rincian_informasi' => 'required|string|max:2000',
            'tujuan_penggunaan' => 'required|string|max:1000',
            'cara_memperoleh' => 'required|array|min:1',
            'cara_memperoleh.*' => 'string',
            'cara_mendapatkan_salinan' => 'nullable|array',
            'cara_mendapatkan_salinan.*' => 'string',
            'lampiran' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_telepon_email.required' => 'No Telepon/Email wajib diisi.',
            'rincian_informasi.required' => 'Rincian informasi yang dibutuhkan wajib diisi.',
            'tujuan_penggunaan.required' => 'Tujuan penggunaan informasi wajib diisi.',
            'cara_memperoleh.required' => 'Cara memperoleh informasi wajib dipilih.',
            'cara_memperoleh.min' => 'Pilih minimal satu cara memperoleh informasi.',
            'lampiran.required' => 'Lampiran KTP/Surat Kuasa/Anggaran Dasar wajib diunggah.',
            'lampiran.mimes' => 'File lampiran harus berformat PDF, JPG, atau PNG.',
            'lampiran.max' => 'Ukuran file lampiran maksimal 5MB.',
        ]);

        try {
            // Store the uploaded file
            $file = $request->file('lampiran');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('permohonan-informasi', $filename, 'local');

            $pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Save to database
            $permohonan = PermohonanInformasi::create([
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'],
                'no_telepon_email' => $validated['no_telepon_email'],
                'rincian_informasi' => $validated['rincian_informasi'],
                'tujuan_penggunaan' => $validated['tujuan_penggunaan'],
                'cara_memperoleh' => $validated['cara_memperoleh'],
                'cara_mendapatkan_salinan' => $validated['cara_mendapatkan_salinan'] ?? [],
                'lampiran_path' => $filePath,
                'status' => 'pending',
                'pin' => $pin,
            ]);

            Log::info('Permohonan Informasi Submitted', ['id' => $permohonan->id, 'nama' => $permohonan->nama, 'kode_unik' => $permohonan->kode_unik]);

            // Optional: Send email notification to admin
            // You can uncomment and configure this if needed
            /*
            Mail::send('emails.permohonan-informasi', $permohonan->toArray(), function ($message) {
                $message->to('admin@bpbumd.jakarta.go.id')
                        ->subject('Permohonan Informasi Publik Baru');
            });
            */

            return back()->with([
                'success' => 'Permohonan informasi berhasil dikirim.',
                'kode_unik' => $permohonan->kode_unik,
                'pin' => $pin
            ]);

        } catch (\Exception $e) {
            Log::error('Error storing permohonan informasi: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan permohonan. Silakan coba lagi.'])
                ->withInput();
        }
    }

    public function pengajuankeberatan()
    {
        return Inertia::render('PPID/StandarLayanan/PengajuanKeberatan');
    }

    public function pengajuanKeberatanForm()
    {
        return Inertia::render('PPID/StandarLayanan/FormPengajuanKeberatan');
    }

    public function storePengajuanKeberatan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string|max:1000',
                'no_telepon' => 'required|string|max:255',
                'pekerjaan' => 'required|string|max:255',
                'nomor_induk_kependudukan' => 'required|numeric|digits:16',
                'nomor_pokok_wajib_pajak' => 'nullable|numeric|digits_between:15,16',
                'alasan_keberatan' => 'required|array|min:1',
                'alasan_keberatan.*' => 'string',
                'kasus_posisi' => 'required|string|max:5000',
            ], [
                'nama.required' => 'Nama wajib diisi.',
                'alamat.required' => 'Alamat wajib diisi.',
                'no_telepon.required' => 'No Telepon wajib diisi.',
                'pekerjaan.required' => 'Pekerjaan wajib diisi.',
                'nomor_induk_kependudukan.required' => 'Nomor Induk Kependudukan wajib diisi.',
                'nomor_induk_kependudukan.digits' => 'Nomor Induk Kependudukan harus 16 digit.',
                'nomor_pokok_wajib_pajak.digits_between' => 'Nomor Pokok Wajib Pajak harus 15 atau 16 digit.',
                'alasan_keberatan.required' => 'Alasan keberatan wajib dipilih.',
                'alasan_keberatan.min' => 'Pilih minimal satu alasan keberatan.',
                'kasus_posisi.required' => 'Kasus posisi wajib diisi.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validated = $validator->validated();

            $pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Save to database
            $keberatan = PengajuanKeberatan::create([
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'],
                'no_telepon' => $validated['no_telepon'],
                'pekerjaan' => $validated['pekerjaan'],
                'nomor_induk_kependudukan' => $validated['nomor_induk_kependudukan'],
                'nomor_pokok_wajib_pajak' => $validated['nomor_pokok_wajib_pajak'] ?? null,
                'alasan_keberatan' => $validated['alasan_keberatan'],
                'kasus_posisi' => $validated['kasus_posisi'],
                'status' => 'pending',
                'pin' => $pin,
            ]);

            Log::info('Pengajuan Keberatan Submitted', ['id' => $keberatan->id, 'nama' => $keberatan->nama, 'kode_unik' => $keberatan->kode_unik]);

            // Optional: Send email notification to admin
            // You can uncomment and configure this if needed
            /*
            Mail::send('emails.pengajuan-keberatan', $keberatan->toArray(), function ($message) {
                $message->to('admin@bpbumd.jakarta.go.id')
                        ->subject('Pengajuan Keberatan Baru');
            });
            */

            return back()->with([
                'success' => 'Pengajuan keberatan berhasil dikirim.',
                'kode_unik' => $keberatan->kode_unik,
                'pin' => $pin
            ]);

        } catch (\Throwable $e) {
            Log::error('Error storing pengajuan keberatan: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan pengajuan. Silakan coba lagi.'])
                ->withInput();
        }
    }

    public function prosedursengketa()
    {
        return Inertia::render('PPID/StandarLayanan/ProsedurSengketa');
    }

    public function biayalayanan()
    {
        return Inertia::render('PPID/StandarLayanan/BiayaLayanan');
    }

    public function trackPengajuan(Request $request)
    {
        $request->validate([
            'kode_unik' => 'required|string',
            'pin' => 'required|string|size:4'
        ]);

        $kodeUnik = strtoupper(trim($request->kode_unik));
        $pin = trim($request->pin);
        
        // Check if it's Permohonan Informasi (PI-) or Pengajuan Keberatan (PK-)
        if (str_starts_with($kodeUnik, 'PI-')) {
            $pengajuan = PermohonanInformasi::with('logs')->where('kode_unik', $kodeUnik)->first();
            $type = 'permohonan_informasi';
        } elseif (str_starts_with($kodeUnik, 'PK-')) {
            $pengajuan = PengajuanKeberatan::with('logs')->where('kode_unik', $kodeUnik)->first();
            $type = 'pengajuan_keberatan';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Format kode tracking tidak valid. Gunakan format PI-XXXXXX atau PK-XXXXXX'
            ], 404);
        }

        if (!$pengajuan) {
            return response()->json([
                'success' => false,
                'message' => 'Kode tracking tidak ditemukan.'
            ], 404);
        }

        if ($pengajuan->pin !== $pin) {
            return response()->json([
                'success' => false,
                'message' => 'PIN tidak valid.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'type' => $type,
                'kode_unik' => $pengajuan->kode_unik,
                'nama' => $pengajuan->nama,
                'status' => $pengajuan->status,
                'status_label' => $pengajuan->status_label,
                'created_at' => $pengajuan->created_at->format('d M Y H:i'),
                'processed_at' => $pengajuan->processed_at ? $pengajuan->processed_at->format('d M Y H:i') : null,
                'admin_notes' => $pengajuan->admin_notes,
                'logs' => $pengajuan->logs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'action' => $log->action,
                        'old_status' => $log->old_status,
                        'new_status' => $log->new_status,
                        'notes' => $log->notes,
                        'user_name' => $log->user ? $log->user->name : 'System',
                        'created_at' => $log->created_at->format('d M Y H:i'),
                    ];
                })
            ]
        ]);
    }
}
