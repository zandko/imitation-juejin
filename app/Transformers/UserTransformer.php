<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['userInfo'];
    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        $read_count = 0;
        $like_count = 0;
        foreach($user->article as $v)
        {
            $read_count += $v['read_count'];
            $like_count += $v['like_count'];
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'email' => $user->email,
            'phone' => $user->phone,
            'provider' => $user->provider,
            'provider_id' => $user->provider_id,
            'following' => count($user->following),
            'followers' => count($user->followers),
            'lable_count' => count($user->userLable),
            'collection' => count($user->collection),
            'read_count' => $read_count,
            'like_count' => $like_count,
            'notification_count' => $user->notification_count,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }

    /**
     * @param User $user
     * @return \League\Fractal\Resource\Item
     */
    public function includeUserInfo(User $user)
    {
        return $this->item($user->userInfo, new UserInfoTransformer());
    }
}