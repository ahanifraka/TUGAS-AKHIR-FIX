<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'upload_categories';

    protected $fillable = [
        'name',
        'is_active',
    ];

    public function __toString(): string
    {
        return (string) ($this->name ?? '');
    }
}