<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Berita;

class BeritaCategory extends Model
{
    use SoftDeletes;
    
    protected $table = 'berita_categories';

    protected $fillable = ['category_name', 'category_slug', 'is_active', 'created_by', 'updated_by'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi ke Berita
    public function beritas(): HasMany
    {
        return $this->hasMany(Berita::class, 'category_id');
    }

    // Relasi ke User yang membuat
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke User yang mengupdate
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function __toString()
    {
        return (string) $this->category_name;
    }
}
