<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'is_published',
        'layout',
        'user_id',
        'category_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function menuItems()
    {
        return $this->morphMany(MenuItem::class, 'linkable');
    }
}
