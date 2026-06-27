<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPeraturan extends Model
{
    protected $table = 'status_peraturan';

    protected $fillable = [
        'name',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope untuk hanya mengambil yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('name', 'asc');
    }
}
