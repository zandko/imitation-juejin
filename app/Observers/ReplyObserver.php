<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\ArticleReply;

class ReplyObserver
{
    /**
     * 回复数量加1
     * 触发消息通知
     * @param Reply $reply
     */
    public function created(Reply $reply)
    {
        $reply->article->increment('reply_count', 1);

        if (!($reply->user->id == $reply->article->user_id)) {
            $reply->article->user->notify(new ArticleReply($reply));
        }
    }

    /**
     * 回复数量减1
     * @param Reply $reply
     */
    public function deleted(Reply $reply)
    {
        if ($reply->article->reply_count > 1) {
            $reply->article->decrement('reply_count', 1);
        }
    }
}