<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'file_path',
        'category',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];
}
