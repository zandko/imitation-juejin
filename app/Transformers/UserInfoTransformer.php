<?php

namespace App\Transformers;

use App\Models\UserInfo;
use League\Fractal\TransformerAbstract;

class UserInfoTransformer extends TransformerAbstract
{
    /**
     * @param UserInfo $userInfo
     * @return array
     */
    public function transform(UserInfo $userInfo)
    {
        return [
            'id' => $userInfo->id,
            'user_id' => $userInfo->user_id,
            'introduction' => $userInfo->introduction,
            'company' => $userInfo->company,
            'position' => $userInfo->position,
            'homepage' => $userInfo->homepage,
        ];
    }

}