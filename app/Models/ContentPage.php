<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentPage extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'content_pages';
  protected $fillable = [
    'title',
    'description',
    'image',
    'slug',
    'subtitle',
    'description2',
    'metadata_title',
    'metadata_keywords',
    'metadata_description',
    'published',
    'upload_files'
  ];

  protected $image_fields = ['image'];
  public $timestamps = true;
}
