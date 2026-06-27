<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Regulasi extends Model
{
    use HasFactory;

    protected $table = 'regulasi';

    protected $fillable = [
        'title',
        'content',
        'file',
        'is_active',
        'tipe_dokumen',
        'judul_peraturan',
        'nomor_peraturan',
        'tahun_peraturan',
        'jenis_peraturan',
        'singkatan_jenis',
        'tempat_penetapan',
        'tanggal_penetapan',
        'tanggal_pengundangan',
        'sumber',
        'subjek',
        'status_peraturan',
        'keterangan_dokumen',
        'teu_badan',
        'bidang_hukum',
        'bahasa',
        'lokasi',
        'keterangan_status',
        'tag',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_penetapan' => 'date',
        'tanggal_pengundangan' => 'date',
    ];

    public function getFileUrlAttribute()
    {
        $file = $this->file;
        if (empty($file)) {
            return null;
        }

        // If already an absolute URL, return as is
        if (str_starts_with($file, 'http://') || str_starts_with($file, 'https://')) {
            return $file;
        }

        // Normalize slashes for Windows paths
        $normalizedFile = str_replace('\\', '/', $file);
        $publicPath = str_replace('\\', '/', public_path());
        $storagePublic = str_replace('\\', '/', storage_path('app/public'));

        // If absolute path inside public/, convert to relative and asset()
        if (str_starts_with($normalizedFile, $publicPath . '/')) {
            $relative = Str::after($normalizedFile, $publicPath . '/');
            return asset($relative);
        }

        // If absolute path inside storage/app/public, convert to /storage/ URL
        if (str_starts_with($normalizedFile, $storagePublic . '/')) {
            $relative = Str::after($normalizedFile, $storagePublic . '/');
            return asset('storage/' . $relative);
        }

        // Otherwise treat as relative path under public
        return asset($file);
    }

    /**
     * Handle file assignment. Accept string path directly.
     */
    public function setFileAttribute($value)
    {
        if (is_string($value)) {
            $trimmed = trim($value);
            $this->attributes['file'] = $trimmed !== '' ? $trimmed : null;
            return;
        }

        $this->attributes['file'] = null;
    }
}