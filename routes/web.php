<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\BeritaCategoryController;
use App\Http\Controllers\BumdController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AlbumImageController;
use App\Http\Controllers\ContentSliderController;
use App\Http\Controllers\ContentPageController;
use App\Http\Controllers\PejabatController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegulasiController;
use App\Http\Controllers\PPIDController;
use App\Http\Controllers\PPIDAdminController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;


// Redirect "/" to default locale prefix (e.g., "/id")
Route::get('/', function (\Illuminate\Http\Request $request) {
    $preferred = $request->cookie('locale', config('app.default_locale', 'id'));
    if (!in_array($preferred, ['id', 'en'])) {
        $preferred = config('app.default_locale', 'id');
    }
    return redirect('/' . $preferred);
})->name('index'); // Changed from 'root.redirect' to 'index' for compatibility

// Localized homepage ("/id" or "/en"). This avoids 404 when redirecting to "/id".
Route::get('/{locale}', [IndexController::class, 'index'])
    ->whereIn('locale', ['id', 'en'])
    ->name('localized.index');

// Locale setter route to persist language selection via server-side encrypted cookie
Route::get('/set-locale/{locale}', function (Request $request, string $locale) {
    if (!in_array($locale, ['id', 'en'], true)) {
        $locale = config('app.default_locale', 'id');
    }
    // Persist 1 year
    Cookie::queue('locale', $locale, 60 * 24 * 365, '/');

    // Optional redirect back to current page
    $redirect = $request->query('redirect');
    if (is_string($redirect) && Str::startsWith($redirect, '/')) {
        // If redirect target is home or a localized home, prefer redirecting to the new locale homepage
        if (preg_match('~^/(?:id|en)?/?$~', $redirect)) {
            return redirect('/' . $locale);
        }
        return redirect($redirect);
    }

    // Fallback: back to previous page, else to localized home
    return redirect()->back()->withInput([]) ?: redirect('/' . $locale);
})->name('set-locale');

// Public Berita Routes
Route::get('/berita', [BeritaController::class, 'indexPublic'])->name('beritas.indexPublic');
Route::get('/berita/{berita:slug}', [BeritaController::class, 'showPublic'])->name('beritas.showPublic');

// Public Pengumuman Routes (BARU)
Route::get('/pengumuman', [PengumumanController::class, 'indexPublic'])->name('pengumuman.indexPublic');
Route::get('/pengumuman/{pengumuman:slug}', [PengumumanController::class, 'showPublic'])->name('pengumuman.showPublic');

// Public BUMD Routes
Route::get('/daftar-bumd-perusahaan-patungan', [BumdController::class, 'daftarKepemilikan'])->name('bumds.daftarKepemilikan');
Route::get('/bumd', [BumdController::class, 'indexPublic'])->name('bumds.indexPublic');
Route::get('/bumd/{bumd:kode}', [BumdController::class, 'showPublic'])->name('bumds.showPublic');

// Public Album Routes
Route::get('/galeri', [AlbumController::class, 'indexPublic'])->name('albums.indexPublic');
Route::get('/galeri/{album}', [AlbumController::class, 'showPublic'])->name('albums.showPublic');

// BPBUMD Profil Routes
Route::get('/tentang-kami', [ProfilController::class, 'tentangkami'])->name('tentangkami.index');
Route::get('/visi-misi', [ProfilController::class, 'visimisi'])->name('visimisi.index');
Route::get('/tugas-pokok-dan-fungsi', [ProfilController::class, 'tupoksi'])->name('tupoksi.index');
Route::get('/struktur-organisasi', [ProfilController::class, 'strukturorganisasi'])->name('strukturorganisasi.index');
Route::get('/pejabat', [ProfilController::class, 'pejabat'])->name('pejabat.index');
Route::get('/pejabat/{id}', [PejabatController::class, 'showPublic'])
    ->name('pejabat.show.public');
Route::get('/rencana-strategis', [ProfilController::class, 'rencanastrategis'])->name('rencanastrategis.index');

// PPID Routes
Route::get('/ppid/profil', [PPIDController::class, 'profil'])->name('ppid.profil');
Route::get('/ppid/visi-misi', [PPIDController::class, 'visimisi'])->name('ppid.visimisi');
Route::get('/ppid/struktur-organisasi', [PPIDController::class, 'strukturorganisasi'])->name('ppid.strukturorganisasi');
Route::get('/ppid/tugas-fungsi', [PPIDController::class, 'tugasfungsi'])->name('ppid.tugasfungsi');
Route::get('/ppid/dasar-hukum', [PPIDController::class, 'dasarhukum'])->name('ppid.dasarhukum');
Route::get('/ppid/informasi-yang-wajib-disediakan-dan-diumumkan-secara-berkala', [PPIDController::class, 'informasiberkala'])->name('ppid.informasiberkala');
Route::get('/ppid/informasi-yang-wajib-tersedia-setiap-saat', [PPIDController::class, 'informasieseluruhwaktu'])->name('ppid.informasieseluruhwaktu');
Route::get('/ppid/informasi-yang-wajib-diumumkan-secara-serta-merta', [PPIDController::class, 'informasiesertamerta'])->name('ppid.informasiesertamerta');
Route::get('/ppid/maklumat-layanan', [PPIDController::class, 'maklumatlayanan'])->name('ppid.maklumatlayanan');
Route::get('/ppid/tata-cara-permohonan-informasi', [PPIDController::class, 'permohonaninformasi'])->name('ppid.permohonaninformasi');
Route::get('/permohonan-informasi', [PPIDController::class, 'permohonanInformasiForm'])->name('ppid.permohonan-informasi.form');
Route::post('/permohonan-informasi', [PPIDController::class, 'storePermohonanInformasi'])->name('ppid.permohonan-informasi.store');
Route::get('/ppid/prosedur-pengajuan-keberatan', [PPIDController::class, 'pengajuankeberatan'])->name('ppid.pengajuankeberatan');
Route::get('/ppid/tata-cara-pengajuan-permohonan', [PPIDController::class, 'prosedursengketa'])->name('ppid.prosedursengketa');
Route::get('/ppid/biaya-layanan', [PPIDController::class, 'biayalayanan'])->name('ppid.biayalayanan');
Route::get('/form-pengajuan-keberatan', [PPIDController::class, 'pengajuanKeberatanForm'])->name('ppid.pengajuan-keberatan.form');
Route::post('/form-pengajuan-keberatan', [PPIDController::class, 'storePengajuanKeberatan'])->name('ppid.pengajuan-keberatan.store');
Route::post('/ppid/track-pengajuan', [PPIDController::class, 'trackPengajuan'])->name('ppid.track-pengajuan');

// Regulasi Routes (Public)
Route::get('/regulasi', [RegulasiController::class, 'indexPublic'])->name('regulasi.indexPublic');
Route::get('/regulasi/{regulasi}', [RegulasiController::class, 'showPublic'])->name('regulasi.showPublic');

// Dashboard Route
Route::get('/dashboard', [IndexController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// FAQ
Route::get('/faq', function () {
    return Inertia\Inertia::render('Faq/Faq');
})->name('faq');

Route::middleware('auth')->group(function () {

    // User Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Resource Routes
    Route::resource('beritas', BeritaController::class);
    Route::resource('berita-categories', BeritaCategoryController::class);
    Route::resource('albums', AlbumController::class);
    Route::resource('albums.images', AlbumImageController::class)->except(['index']);
    Route::resource('bumds', BumdController::class)->middleware('role:editor|admin');
    Route::resource('content-sliders', ContentSliderController::class)->middleware('role:editor|admin');
    Route::resource('content-pages', ContentPageController::class)->middleware('role:editor|admin');
    Route::resource('pejabats', PejabatController::class)->middleware('role:editor|admin');
    Route::resource('regulasis', RegulasiController::class)->middleware('role:editor|admin');
    Route::resource('tipe-dokumen', \App\Http\Controllers\TipeDokumenController::class)->only(['index', 'store', 'update', 'destroy'])->middleware('role:admin');
    Route::resource('status-peraturan', \App\Http\Controllers\StatusPeraturanController::class)->only(['index', 'store', 'update', 'destroy'])->middleware('role:admin');
    Route::resource('users', UserController::class)->middleware('role:admin');
    Route::prefix('admin')->name('admin.')->middleware('role:editor|admin')->group(function () {
        Route::resource('pengumuman', PengumumanController::class)->except(['index', 'show']);
        Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
        Route::get('/pengumuman/{pengumuman}', [PengumumanController::class, 'show'])->name('pengumuman.show');
    });

    // Pejabat Reorder Routes
    Route::get('pejabats-reorder', [PejabatController::class, 'reorder'])->name('pejabats.reorder')->middleware('role:editor|admin');
    Route::post('pejabats-update-order', [PejabatController::class, 'updateOrder'])->name('pejabats.update-order')->middleware('role:editor|admin');

    // Activity Logs
    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index')->middleware('role:admin');

    // Media Library
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');

    // PPID Admin Routes
    Route::prefix('admin/ppid')->name('admin.ppid.')->middleware('role:editor|admin')->group(function () {
        Route::get('/dashboard', [PPIDAdminController::class, 'dashboard'])->name('dashboard');

        // Permohonan Informasi Routes
        Route::get('/permohonan-informasi', [PPIDAdminController::class, 'permohonanInformasiIndex'])->name('permohonan-informasi');
        Route::get('/permohonan-informasi/{permohonanInformasi}', [PPIDAdminController::class, 'permohonanInformasiShow'])->name('permohonan-informasi.show');
        Route::patch('/permohonan-informasi/{permohonanInformasi}', [PPIDAdminController::class, 'permohonanInformasiUpdate'])->name('permohonan-informasi.update');
        Route::get('/permohonan-informasi/{permohonanInformasi}/download', [PPIDAdminController::class, 'downloadAttachment'])->name('permohonan-informasi.download');

        // Pengajuan Keberatan Routes
        Route::get('/pengajuan-keberatan', [PPIDAdminController::class, 'pengajuanKeberatanIndex'])->name('pengajuan-keberatan');
        Route::get('/pengajuan-keberatan/{pengajuanKeberatan}', [PPIDAdminController::class, 'pengajuanKeberatanShow'])->name('pengajuan-keberatan.show');
        Route::patch('/pengajuan-keberatan/{pengajuanKeberatan}', [PPIDAdminController::class, 'pengajuanKeberatanUpdate'])->name('pengajuan-keberatan.update');
    });

});

require __DIR__ . '/auth.php';

// Old URL redirects
require __DIR__ . '/redirects.php';

// Custom Content Page Routes
Route::get('/{slug}', [ContentPageController::class, 'showPublicBySlug'])
    ->where('slug', '^(?!beritas|berita-categories|albums|bumds|content-sliders|content-pages|pejabats|regulasis|tipe-dokumen|status-peraturan|users|pejabats-reorder|pejabats-update-order|logs|media|admin|dashboard|profile|login|register|password|verify-email|set-locale).*')
    ->name('contentpages.showPublicBySlug');