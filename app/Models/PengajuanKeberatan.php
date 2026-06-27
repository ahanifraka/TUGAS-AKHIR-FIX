<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengajuanKeberatan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_keberatan';

    protected $fillable = [
        'kode_unik',
        'nama',
        'alamat',
        'pekerjaan',
        'no_telepon',
        'nomor_induk_kependudukan',
        'nomor_pokok_wajib_pajak',
        'alasan_keberatan',
        'kasus_posisi',
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
            // Format: PK-YYMMDD-XXXX (PK = Pengajuan Keberatan)
            $kode = 'PK-' . date('ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('kode_unik', $kode)->exists());

        return $kode;
    }

    protected $casts = [
        'alasan_keberatan' => 'array',
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

    public function getAlasanKeberatanLabelsAttribute()
    {
        $labels = [
            'permohonan_informasi_ditolak' => 'Permohonan Informasi ditolak',
            'informasi_berkala_tidak_disediakan' => 'Informasi berkala tidak disediakan',
            'permintaan_informasi_tidak_ditanggapi' => 'Permintaan Informasi tidak ditanggapi',
            'permintaan_informasi_ditanggapi_tidak_sebagaimana_diminta' => 'Permintaan Informasi ditanggapi tidak sebagaimana yang diminta',
            'permintaan_informasi_tidak_dipenuhi' => 'Permintaan Informasi tidak dipenuhi',
            'biaya_yang_dikenakan_tidak_wajar' => 'Biaya yang dikenakan tidak wajar',
            'informasi_disampaikan_melebihi_jangka_waktu_ditentukan' => 'Informasi disampaikan melebihi jangka waktu yang ditentukan',
        ];

        return collect($this->alasan_keberatan)->map(function ($alasan) use ($labels) {
            return $labels[$alasan] ?? $alasan;
        })->toArray();
    }

    /**
     * Get the activity logs for the pengajuan keberatan.
     */
    public function logs()
    {
        return $this->hasMany(PengajuanKeberatanLog::class)->orderBy('created_at', 'desc');
    }
}
