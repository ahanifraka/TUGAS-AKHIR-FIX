<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ContentSlider extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'content_sliders';

    protected $fillable = [
        'title',
        'description',
        'image',
        'link',
        'published',
        'created_by',
        'updated_by',
    ];

    /**
     * Fields that are translatable (stored as JSON translations)
     *
     * @var array<int, string>
     */
    public $translatable = ['title', 'description'];

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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function __toString()
    {
        return (string) ($this->title ?? 'Slider');
    }
}