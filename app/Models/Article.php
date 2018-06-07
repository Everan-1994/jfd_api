<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'up_body',
        'down_body', 'views', 'true_views',
        'asks', 'true_asks', 'status',
        'author_id', 'phone'
    ];

    public function tags()
    {
        return $this->hasMany(Tag::class, 'article_id', 'id');
    }
}