<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pejabat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pejabat';
    protected $fillable = [
        'nama',
        'ttl',
        'image',
        'pendidikan',
        'jabatan',
        'description',
        'order',
        'published'
    ];

    protected $image_fields = ['image'];

    protected $casts = [
        'published' => 'boolean',
    ];

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }
            return asset($this->image);
        }
        return asset('images/default-cover.png');
    }

    public function __toString()
    {
        return (string) ($this->nama ?? 'Pejabat');
    }
}