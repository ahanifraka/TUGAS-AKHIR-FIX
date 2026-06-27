<?php
// ...existing code...

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KondisiKeuangan extends Model
{
    // pastikan nama tabel sesuai nama di database
    protected $table = 'kondisi_keuangans';

    // jika tabel tidak pakai timestamps, non-aktifkan
    public $timestamps = false;

    protected $fillable = [
        'bumd_id',
        'no_akun',
        'nama_akun',
        'tahun_2020',
        'tahun_2021',
        'tahun_2022',
        'tahun_2023',
        'tahun_2024',
    ];

    protected $casts = [
        'tahun_2020' => 'integer',
        'tahun_2021' => 'integer',
        'tahun_2022' => 'integer',
        'tahun_2023' => 'integer',
        'tahun_2024' => 'integer',
    ];
}