<?php

namespace App\Observers;

use App\Models\Message;

class MessageObserver
{
    public function created(Message $message)
    {
        \DB::table('articles')->whereId($message->article_id)->increment('asks');
    }
}