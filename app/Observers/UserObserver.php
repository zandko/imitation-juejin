<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * 默认头像
     * @param User $user
     */
    public function saving(User $user)
    {
        if(empty($user->avatar)) {
            $user->avarar = 'http://img.zcool.cn/community/015b9f57e0ff170000012e7e1395fb.jpg@2o.jpg';
            $user->save();
        }
    }
}