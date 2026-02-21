<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_published',
        'layout',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function menuItems()
    {
        return $this->morphMany(\App\Models\MenuItem::class, 'linkable');
    }
}
