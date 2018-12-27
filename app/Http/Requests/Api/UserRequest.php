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
