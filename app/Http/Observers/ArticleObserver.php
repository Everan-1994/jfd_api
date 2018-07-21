<?php

namespace App\Observers;

use App\Models\Article;

class ArticleObserver
{
    public function creating(Article $article)
    {
        $article->up_body = clean($article->up_body, 'user_articles_body');
        $article->down_body = clean($article->down_body, 'user_articles_body');
    }

    public function updating(Article $article)
    {
        $article->up_body = clean($article->up_body, 'user_articles_body');
        $article->down_body = clean($article->down_body, 'user_articles_body');
    }

    public function deleted(Article $article)
    {
        \DB::table('messages')->where('article_id', $article->id)->delete();
    }
}