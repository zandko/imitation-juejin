<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    /**
     * 授权策略
     * @param User $user
     * @param Reply $reply
     * @return bool
     */
    public function own(User $user, Reply $reply)
    {
        return $user->id == $reply->article->user_id;
    }
}
