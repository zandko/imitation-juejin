1、创建 `UserRequest`

```php
<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;
use Auth;

class UserRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'name' => 'required|between:6,20|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name',
                    'password' => 'required|string|between:6,20',
                    'phone' => 'required',
                    'verification_key' => 'required|string',
                    'verification_code' => 'required|string',
                ];
                break;

            case 'PATCH':
                $user_id = Auth::guard('api')->id();
                return [
                    'avatar_image_id' => 'exists:images,id,type,avatar,user_id,' . $user_id,
                    'introduction' => 'nullable|max:80',
                    'company' => 'nullable|max:20',
                    'position' => 'nullable|max:20',
                    'homepage' => 'nullable|url',
                ];
                break;
        }

    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'verification_key' => '短信验证码 key',
            'verification_code' => '短信验证码',
            'introduction' => '个人介绍',
            'company' => '公司',
            'position' => '职位',
            'homepage' => '个人主页'
        ];
    }

}

```

2、创建 `UserTransformer`

```php
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
```

3、实际业务代码

```php
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
```

