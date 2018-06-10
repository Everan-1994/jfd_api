<?php

namespace App\Observers;

use App\Models\Message;

class MessageObserver
{
    public function created(Message $message)
    {
        \DB::table('articles')->whereId($message->article_id)->increment('asks', 1, ['true_asks' => \DB::raw('true_asks  + 1')]);
    }

    public function deleted(Message $message)
    {
    	\DB::table('articles')->whereId($message->article_id)->decrement('asks', 1, ['true_asks' => \DB::raw('true_asks  - 1')]);
    }
}