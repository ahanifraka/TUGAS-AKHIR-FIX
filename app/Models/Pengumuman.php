<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Pengumuman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'slug',
        'konten',
        'excerpt',
        'gambar',
        'dokumen',
        'nomor_pengumuman',
        'tanggal_terbit',
        'tanggal_berakhir',
        'is_penting',
        'is_aktif',
        'tipe',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_berakhir' => 'date',
        'is_penting' => 'boolean',
        'is_aktif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'image_url',
        'document_url',
        'thumbnail_url',
        'formatted_tanggal_terbit',
        'formatted_tanggal_berakhir',
        'status_label',
        'tipe_label',
        'is_expired',
        'can_view',
    ];

    /**
     * Get the URL for the image.
     */

    public function getImageUrlAttribute()
    {
        if (!$this->gambar) {
            return asset('images/default-cover.png');
        }

        // Jika sudah full URL
        if (filter_var($this->gambar, FILTER_VALIDATE_URL)) {
            return $this->gambar;
        }

        // Jika sudah diawali storage/
        if (str_starts_with($this->gambar, 'storage/')) {
            return asset($this->gambar);
        }

        // Jika hanya pengumuman/xxx.webp
        return asset('storage/' . ltrim($this->gambar, '/'));
    }


    /**
     * Get the URL for the document.
     */
    protected function documentUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->dokumen) {
                    return null;
                }

                // Jika sudah full URL
                if (filter_var($this->dokumen, FILTER_VALIDATE_URL)) {
                    return $this->dokumen;
                }

                // Jika diawali dengan storage/
                if (str_starts_with($this->dokumen, 'storage/')) {
                    return asset($this->dokumen);
                }

                // Jika diawali dengan /storage/
                if (str_starts_with($this->dokumen, '/storage/')) {
                    return asset($this->dokumen);
                }

                // Jika hanya nama file
                if (!str_contains($this->dokumen, '/')) {
                    return asset('storage/pengumuman/dokumen/' . $this->dokumen);
                }

                // Default
                return asset('storage/' . ltrim($this->dokumen, '/'));
            }
        );
    }

    /**
     * Get thumbnail URL (smaller version of image)
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $imageUrl = $this->image_url;

                // Jika menggunakan default image, return as is
                if (str_contains($imageUrl, 'default-cover.png')) {
                    return $imageUrl;
                }

                // Untuk sekarang, return image_url yang sama
                // Di masa depan bisa implement thumbnail generation
                return $imageUrl;
            }
        );
    }

    /**
     * Get formatted tanggal terbit.
     */
    protected function formattedTanggalTerbit(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->tanggal_terbit) {
                    return null;
                }

                // Format: 24 Desember 2024
                return $this->tanggal_terbit->translatedFormat('d F Y');
            }
        );
    }

    /**
     * Get formatted tanggal berakhir.
     */
    protected function formattedTanggalBerakhir(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->tanggal_berakhir) {
                    return null;
                }

                return $this->tanggal_berakhir->translatedFormat('d F Y');
            }
        );
    }

    /**
     * Get status label with color.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->is_aktif) {
                    return [
                        'label' => 'Nonaktif',
                        'color' => 'danger',
                        'class' => 'bg-red-100 text-red-800 px-2 py-1 rounded text-xs'
                    ];
                }

                if ($this->is_expired) {
                    return [
                        'label' => 'Kadaluarsa',
                        'color' => 'warning',
                        'class' => 'bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs'
                    ];
                }

                return [
                    'label' => 'Aktif',
                    'color' => 'success',
                    'class' => 'bg-green-100 text-green-800 px-2 py-1 rounded text-xs'
                ];
            }
        );
    }

    /**
     * Get tipe label with color.
     */
    protected function tipeLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $colors = [
                    'pengumuman' => ['label' => 'Pengumuman', 'color' => 'blue', 'class' => 'bg-blue-100 text-blue-800'],
                    'pemberitahuan' => ['label' => 'Pemberitahuan', 'color' => 'yellow', 'class' => 'bg-yellow-100 text-yellow-800'],
                    'undangan' => ['label' => 'Undangan', 'color' => 'purple', 'class' => 'bg-purple-100 text-purple-800'],
                    'lowongan' => ['label' => 'Lowongan', 'color' => 'green', 'class' => 'bg-green-100 text-green-800'],
                    'laporan' => ['label' => 'Laporan', 'color' => 'indigo', 'class' => 'bg-indigo-100 text-indigo-800'],
                    'lainnya' => ['label' => 'Lainnya', 'color' => 'gray', 'class' => 'bg-gray-100 text-gray-800'],
                ];

                return $colors[$this->tipe] ?? $colors['lainnya'];
            }
        );
    }

    /**
     * Check if announcement is expired.
     */
    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->tanggal_berakhir) {
                    return false;
                }

                return now()->greaterThan($this->tanggal_berakhir);
            }
        );
    }

    /**
     * Check if announcement can be viewed publicly.
     */
    protected function canView(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Cek apakah aktif
                if (!$this->is_aktif) {
                    return false;
                }

                // Cek apakah tanggal terbit sudah lewat
                if ($this->tanggal_terbit && $this->tanggal_terbit->greaterThan(now())) {
                    return false;
                }

                // Cek apakah sudah expired
                if ($this->is_expired) {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * Get document file name.
     */
    public function getDocumentNameAttribute()
    {
        if (!$this->dokumen) {
            return null;
        }

        return basename($this->dokumen);
    }

    /**
     * Get document file size.
     */
    public function getDocumentSizeAttribute()
    {
        if (!$this->dokumen) {
            return null;
        }

        try {
            $path = str_replace('storage/', 'public/', $this->dokumen);
            if (Storage::exists($path)) {
                $bytes = Storage::size($path);

                // Convert to human readable format
                if ($bytes >= 1073741824) {
                    return number_format($bytes / 1073741824, 2) . ' GB';
                } elseif ($bytes >= 1048576) {
                    return number_format($bytes / 1048576, 2) . ' MB';
                } elseif ($bytes >= 1024) {
                    return number_format($bytes / 1024, 2) . ' KB';
                } else {
                    return $bytes . ' bytes';
                }
            }
        } catch (\Exception $e) {
            // Silent fail
        }

        return null;
    }

    /**
     * Get document file extension.
     */
    public function getDocumentExtensionAttribute()
    {
        if (!$this->dokumen) {
            return null;
        }

        return pathinfo($this->dokumen, PATHINFO_EXTENSION);
    }

    /**
     * Scope untuk pengumuman aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true)
            ->where('tanggal_terbit', '<=', now())
            ->where(function ($q) {
                $q->whereNull('tanggal_berakhir')
                    ->orWhere('tanggal_berakhir', '>=', now());
            });
    }

    /**
     * Scope untuk pengumuman penting
     */
    public function scopePenting($query)
    {
        return $query->where('is_penting', true);
    }

    /**
     * Scope filter by tipe
     */
    public function scopeByTipe($query, $tipe)
    {
        if ($tipe) {
            return $query->where('tipe', $tipe);
        }
        return $query;
    }

    /**
     * Scope untuk pengumuman yang akan datang
     */
    public function scopeUpcoming($query)
    {
        return $query->where('tanggal_terbit', '>', now());
    }

    /**
     * Scope untuk pengumuman yang sudah expired
     */
    public function scopeExpired($query)
    {
        return $query->where('tanggal_berakhir', '<', now());
    }

    /**
     * Scope search by keyword
     */
    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            return $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', '%' . $keyword . '%')
                    ->orWhere('konten', 'like', '%' . $keyword . '%')
                    ->orWhere('excerpt', 'like', '%' . $keyword . '%')
                    ->orWhere('nomor_pengumuman', 'like', '%' . $keyword . '%');
            });
        }
        return $query;
    }

    /**
     * Relationships
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Auto generate slug on create and update
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul);

                // Ensure uniqueness
                $count = 1;
                $originalSlug = $model->slug;
                while (static::where('slug', $model->slug)->withTrashed()->exists()) {
                    $model->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('judul')) {
                $model->slug = Str::slug($model->judul);

                // Ensure uniqueness
                $count = 1;
                $originalSlug = $model->slug;
                while (
                    static::where('slug', $model->slug)
                        ->where('id', '!=', $model->id)
                        ->withTrashed()
                        ->exists()
                ) {
                    $model->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });

        // Delete associated files when pengumuman is deleted
        static::deleting(function ($model) {
            if ($model->isForceDeleting()) {
                // Delete image file
                if ($model->gambar) {
                    $path = str_replace('storage/', '', $model->gambar);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }

                // DELETE DOKUMEN (BIAR AMAN, ikut dibenerin)
                if ($model->dokumen) {
                    $docPath = str_replace('storage/', '', $model->dokumen);
                    if (Storage::disk('public')->exists($docPath)) {
                        Storage::disk('public')->delete($docPath);
                    }
                }
            }
        });
    }
}