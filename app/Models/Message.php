<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'name', 'phone', 'share_id', 'article_id', 'status', 'ip', 'home_type'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'share_id', 'id');
    }
}
