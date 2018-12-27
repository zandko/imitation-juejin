<?php

namespace App\Http\Controllers\Api;

use App\Notifications\UserFollow;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use App\Models\Follow;

class FollowsController extends Controller
{
    /**
     * 关注 取消关注
     * @param Request $request
     * @return mixed
     */
    public function followed(Request $request,Follow $follow)
    {
        $followed = Auth::guard('api')->user()->followThisUser($request->followed_id);

        $userFollow = User::find($request->followed_id);

        //如果用户关注了另一个用户
        if (count($followed['attached']) > 0) {
            //可以去通知用户 修改用户的关注人数等数据
//            return $this->response->array([
//                'followed' => true,
//            ])->setStatusCode(201);
            $userFollow->notify(new UserFollow($userFollow));
            return $this->response->created();
        } else {
            return $this->response->noContent();
        }
    }

    /**
     * 关注了
     * @return mixed
     */
    public function following(User $user)
    {
        return $user->following;
    }

    /**
     * 关注者
     * @return mixed
     */
    public function followers(User $user)
    {
        return $user->followers;
    }
}
