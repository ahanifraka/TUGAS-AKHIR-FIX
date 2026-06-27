<?php

use Illuminate\Support\Facades\Route;

// ============================================================================
// Redirects from old URLs to new URLs (301 Permanent Redirects)
// ============================================================================

// Livewire POST redirect
Route::redirect('/web/permohonan-informasi', '/permohonan-informasi', 301);
Route::redirect('/web/permohonan-informasi-store', '/permohonan-informasi', 301);
Route::redirect('/web', '/', 301);
Route::redirect('/web/tupoksi', '/tugas-pokok-dan-fungsi', 301);
Route::redirect('/web/visi-misi', '/visi-misi', 301);
Route::redirect('/web/agenda', '/', 301);
Route::redirect('/web/statistik', '/', 301);
Route::redirect('/web/index', '/', 301);
Route::redirect('/web/struktur-organisasi', '/struktur-organisasi', 301);
Route::redirect('/web/rencana-strategis', '/rencana-strategis', 301);
Route::redirect('/web/pejabat', '/pejabat', 301);
Route::get('/web/pejabat/show/{id}', function($id) {
    return redirect("/pejabat/{$id}", 301);
});
Route::redirect('/web/galeri', '/galeri', 301);
Route::get('/web/galeri-show/{album_id}', function($album_id) {
    return redirect("/galeri/{$album_id}", 301);
});
Route::redirect('/web/regulasi', '/regulasi', 301);
Route::redirect('/web/tata-cara-permohonan-informasi', '/ppid/tata-cara-permohonan-informasi', 301);
Route::redirect('/web/daftar-informasi-publik', '/ppid/informasi-yang-wajib-disediakan-dan-diumumkan-secara-berkala', 301);
Route::redirect('/web/informasi-yang-wajib-disediakan-dan-diumumkan-secara-berkala', '/ppid/informasi-yang-wajib-disediakan-dan-diumumkan-secara-berkala', 301);
Route::redirect('/web/informasi-yang-wajib-tersedia-setiap-saat', '/ppid/informasi-yang-wajib-tersedia-setiap-saat', 301);
Route::redirect('/web/informasi-yang-wajib-diumumkan-secara-serta-merta', '/ppid/informasi-yang-wajib-diumumkan-secara-serta-merta', 301);
Route::redirect('/web/maklumat-layanan', '/ppid/maklumat-layanan', 301);
Route::redirect('/web/prosedur-pengajuan-keberatan', '/ppid/prosedur-pengajuan-keberatan', 301);
Route::redirect('/web/biaya-layanan', '/ppid/biaya-layanan', 301);
Route::redirect('/web/profil-ppid', '/ppid/profil', 301);
Route::redirect('/web/visi-dan-misi-ppid', '/ppid/visi-misi', 301);
Route::redirect('/web/struktur-organisasi-ppid', '/ppid/struktur-organisasi', 301);
Route::redirect('/web/tugas-dan-fungsi-ppid', '/ppid/tugas-fungsi', 301);
Route::redirect('/web/dasar-dan-hukum-ppid', '/ppid/dasar-hukum', 301);
Route::redirect('/web/form-pengajuan-keberatan', '/form-pengajuan-keberatan', 301);
Route::redirect('/web/info-permohonan', '/permohonan-informasi', 301);
Route::redirect('/web/tata-cara-pengajuan-keberatan', '/ppid/prosedur-pengajuan-keberatan', 301);
Route::redirect('/web/tata-cara-pengajuan-permohonan', '/ppid/tata-cara-pengajuan-permohonan', 301);
Route::redirect('/web/berita', '/berita', 301);
Route::get('/web/berita/kategori/{id}', function($id) {
    return redirect("/berita?kategori={$id}", 301);
});
Route::get('/web/berita/show/{id}', function($id) {
    return redirect("/berita/{$id}", 301);
});
Route::redirect('/web/berita/search', '/berita', 301);
Route::get('/web/dokumen/{id?}', function($id = null) {
    return redirect('/regulasi', 301);
});
Route::get('/web/bumd/{kode?}', function($kode = null) {
    return $kode ? redirect("/bumd/{$kode}", 301) : redirect('/bumd', 301);
});
Route::redirect('/web/kinerja-bumd', '/bumd', 301);
Route::get('/web/kinerja-bumd/show/{id}', function($id) {
    return redirect("/bumd/{$id}", 301);
});
Route::redirect('/web/dokumen-bumd', '/bumd', 301);
Route::get('/web/dokumen-bumd/show/{id}', function($id) {
    return redirect("/bumd/{$id}", 301);
});
Route::get('/web/kinerja-bumd2/{kode?}', function($kode = null) {
    return $kode ? redirect("/bumd/{$kode}", 301) : redirect('/bumd', 301);
});
Route::redirect('/web/kinerja-keuangan-bumd', '/bumd', 301);
Route::redirect('/web/daftar-bumd-perusahaan-patungan', '/daftar-bumd-perusahaan-patungan', 301);
Route::get('/web/download/{slug}', function($slug) {
    return redirect("/regulasi", 301);
});

// ============================================================================
// Mobile Redirects
// ============================================================================

Route::redirect('/mobile', '/', 301);
Route::redirect('/mobile/menu', '/', 301);
Route::redirect('/mobile/main-menu', '/', 301);
Route::redirect('/mobile/index', '/', 301);

Route::redirect('/mobile/profile', '/tentang-kami', 301);
Route::redirect('/mobile/visi-misi', '/visi-misi', 301);
Route::redirect('/mobile/tupoksi', '/tugas-pokok-dan-fungsi', 301);
Route::redirect('/mobile/struktur-organisasi', '/struktur-organisasi', 301);
Route::redirect('/mobile/pejabat', '/pejabat', 301);
Route::redirect('/mobile/rencana-strategis', '/rencana-strategis', 301);

Route::redirect('/mobile/informasi-bumd', '/bumd', 301);
Route::redirect('/mobile/layanan-informasi-publik', '/ppid/profil', 301);
Route::redirect('/mobile/tata-cara-permohonan-informasi', '/ppid/tata-cara-permohonan-informasi', 301);
Route::redirect('/mobile/daftar-informasi-publik', '/ppid/informasi-yang-wajib-disediakan-dan-diumumkan-secara-berkala', 301);
Route::redirect('/mobile/info-permohonan', '/permohonan-informasi', 301);

Route::redirect('/mobile/tata-cara-pengajuan-keberatan', '/ppid/prosedur-pengajuan-keberatan', 301);
Route::redirect('/mobile/tata-cara-pengajuan-permohonan', '/ppid/tata-cara-pengajuan-permohonan', 301);

Route::redirect('/mobile/berita', '/berita', 301);
Route::get('/mobile/berita/kategori/{id}', function($id) {
    return redirect("/berita?kategori={$id}", 301);
});
Route::get('/mobile/berita/show/{id}', function($id) {
    return redirect("/berita/{$id}", 301);
});
Route::match(['get', 'post'], '/mobile/berita/search', function() {
    return redirect('/berita', 301);
});

Route::redirect('/mobile/galeri', '/galeri', 301);
Route::get('/mobile/galeri-show/{album_id}', function($album_id) {
    return redirect("/galeri/{$album_id}", 301);
});

Route::redirect('/mobile/regulasi', '/regulasi', 301);
Route::redirect('/mobile/agenda', '/', 301);

Route::get('/mobile/bumd/{kode?}', function($kode = null) {
    return $kode ? redirect("/bumd/{$kode}", 301) : redirect('/bumd', 301);
});

Route::redirect('/mobile/kinerja-bumd', '/bumd', 301);
Route::get('/mobile/kinerja-bumd/show/{id}', function($id) {
    return redirect("/bumd/{$id}", 301);
});

Route::redirect('/mobile/dokumen-bumd', '/bumd', 301);
Route::get('/mobile/dokumen-bumd/show/{id}', function($id) {
    return redirect("/bumd/{$id}", 301);
});

Route::get('/mobile/kinerja-bumd2/{kode?}', function($kode = null) {
    return $kode ? redirect("/bumd/{$kode}", 301) : redirect('/bumd', 301);
});

Route::redirect('/mobile/kinerja-keuangan-bumd', '/bumd', 301);
