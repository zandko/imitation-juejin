<?php

namespace App\Http\Controllers\Api;

use App\Models\UserFollow;
use App\Models\User;
use App\Models\Image;
use App\Http\Requests\Api\UserRequest;
use App\Models\UserInfo;
use App\Transformers\UserInfoTransformer;
use App\Transformers\UserTransformer;
use Cache;
use Auth;

class UsersController extends Controller
{
    /**
     * 注册
     * @param UserRequest $request
     * @return \Dingo\Api\Http\Response|void
     */
    public function store(UserRequest $request)
    {
        /*手机验证码*/
        $data = Cache::get($request->verification_key);

        if (!$data) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($data['code'], $request->verification_code)) {
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $data['phone'],
            'password' => bcrypt($request->password),
        ]);

        Cache::forget($request->verification_key);

        /*注册后直接登录*/
        return $this->response->item($user, new UserTransformer())
            ->setMeta([
                'access_token' => Auth::guard('api')->fromUser($user),
                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
            ])
            ->setStatusCode(201);
    }

    /**
     * 用户详情
     * @return \Dingo\Api\Http\Response
     */
    public function show()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    /**
     * 修改用户信息
     * @param UserRequest $request
     * @param UserInfo $userInfo
     * @return \Dingo\Api\Http\Response
     */
    public function update(UserRequest $request, UserInfo $userInfo)
    {
        $user = Auth::guard('api')->user();

        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);
            $user->update([
                'avatar' => $image->path,
            ]);
        }

        if ($userInfos = $userInfo->query()->where('user_id', $user->id)->first()) {
            $userInfos
                ->where('user_id', $user->id)
                ->update([
                    'introduction' => $request->introduction,
                    'company' => $request->company,
                    'position' => $request->position,
                    'homepage' => $request->homepage,
                ]);

            return $this->response->item($userInfos, new UserInfoTransformer());

        } else {
            $userInfo->fill($request->all());
            $userInfo->user_id = $user->id;
            $userInfo->save();

            return $this->response->item($userInfo, new UserInfoTransformer())
                ->setStatusCode(201);
        }
    }

    /**
     * 我的赞
     * @param User $user
     * @return mixed
     */
    public function like(User $user)
    {
        return $this->response->array($user->like)->setStatusCode(201);
    }

    /**
     * 关注的标签
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function lable(User $user)
    {
        return $user->userLable;
    }

    /**
     * 我的收藏
     * @param User $user
     * @return mixed
     */
    public function collection(User $user)
    {
        return $user->collection;
    }
}
