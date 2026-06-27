<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Models\BeritaCategory;

class Berita extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $table = 'beritas';

    protected $fillable = [
        'title',
        'slug',
        'teaser',
        'content',
        'image',
        'published',
        'published_at',
        'category_id',
        'meta_title',
        'meta_keyword',
        'meta_content',
        'popular',
        'created_by',
        'updated_by'
    ];

    /**
     * Fields that are translatable (stored as JSON translations).
     *
     * @var array<int,string>
     */
    public $translatable = [
        'title',
        'teaser',
        'content',
        'meta_title',
        'meta_keyword',
        'meta_content',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Scope a query to only include published beritas.
     */
    public function scopePublished($query)
    {
        return $query->where('published', 1)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope a query to only include scheduled beritas.
     */
    public function scopeScheduled($query)
    {
        return $query->where('published', 1)
            ->whereNotNull('published_at')
            ->where('published_at', '>', now());
    }

    protected $image_fields = ['image'];

    public function getImageUrlAttribute()
    {
        // Safely return image URL or default cover
        if (!empty($this->image)) {
            // If already an absolute URL, return as is
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }
            // Otherwise treat as path under public
            return asset($this->image);
        }
        return asset('images/default-cover.png');
    }

    public function __toString()
    {
        return $this->title;
    }

    public function category()
    {
        return $this->belongsTo(BeritaCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
