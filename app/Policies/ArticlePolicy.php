<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    /**
     * 授权策略
     * @param User $user
     * @param Article $article
     * @return bool
     */
    public function own(User $user,Article $article)
    {
        return $article->user_id == $user->id;
    }
}
