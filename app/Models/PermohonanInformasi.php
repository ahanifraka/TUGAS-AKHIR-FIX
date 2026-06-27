<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermohonanInformasi extends Model
{
    use HasFactory;

    protected $table = 'permohonan_informasi';

    protected $fillable = [
        'kode_unik',
        'nama',
        'alamat',
        'no_telepon_email',
        'rincian_informasi',
        'tujuan_penggunaan',
        'cara_memperoleh',
        'cara_mendapatkan_salinan',
        'lampiran_path',
        'status',
        'admin_notes',
        'processed_at',
        'pin'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_unik)) {
                $model->kode_unik = self::generateKodeUnik();
            }
        });
    }

    /**
     * Generate unique tracking code
     */
    private static function generateKodeUnik()
    {
        do {
            // Format: PI-YYMMDD-XXXX (PI = Permohonan Informasi)
            $kode = 'PI-' . date('ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('kode_unik', $kode)->exists());

        return $kode;
    }

    protected $casts = [
        'cara_memperoleh' => 'array',
        'cara_mendapatkan_salinan' => 'array',
        'processed_at' => 'datetime',
    ];

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processed' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu',
            'processed' => 'Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    /**
     * Get the activity logs for the permohonan.
     */
    public function logs()
    {
        return $this->hasMany(PermohonanInformasiLog::class)->orderBy('created_at', 'desc');
    }
}
