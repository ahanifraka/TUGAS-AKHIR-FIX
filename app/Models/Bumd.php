<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PengurusBumd;
use App\Models\KondisiKeuangan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Bumd extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bumd';

    protected $fillable = [
        'kode',
        'nama_pendek',
        'nama',
        'kategory',
        'sektor',
        'bidang_usaha',
        'hasil_usaha',
        'alamat',
        'hotline',
        'telp',
        'fax',
        'email',
        'website',
        'visi',
        'misi',
        'tujuan',
        'logo',
        'akta_pendirian',
        'akta_perubahan',
        'dasar_hukum',
        'nilai_saham',
        'nama',
        'jabatan',
        'grup',
        'bumd_id',
        'no_akun',
        'nama_akun',
        'tahun_2020',
        'tahun_2021',
        'tahun_2022',
        'tahun_2023',
        'tahun_2024',
        'created_by',
        'updated_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function pengurusBumds()
    {
        return $this->hasMany(PengurusBumd::class);
    }

    public function kondisiKeuangan()
    {
        return $this->hasMany(KondisiKeuangan::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected $appends = [
        'pengurus',
        'kondisi_keuangan'
    ];

    public function getPengurusAttribute()
    {
        // Muat relasi jika belum ada
        if (!$this->relationLoaded('pengurusBumds')) {
            $this->load('pengurusBumds');
        }
        return $this->getRelation('pengurusBumds');
    }

    public function getKondisiKeuanganAttribute()
    {
        if (!$this->relationLoaded('kondisiKeuangan')) {
            $this->load('kondisiKeuangan');
        }
        return $this->getRelation('kondisiKeuangan');
    }

    public function __toString(): string
    {
        return (string) ($this->nama ?? $this->nama_pendek ?? $this->kode);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'kode';
    }
}