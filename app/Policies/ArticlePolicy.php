<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function destroy(User $user,  Article $article)
    {
        return $user->identify < 3 || $user->isAuthorOf($article);
    }
}
