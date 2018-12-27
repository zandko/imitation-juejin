<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Http\Requests\Api\AuthorizationRequest;

class AuthorizationsController extends Controller
{
    /**
     * @param $token
     * @return mixed
     */
    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ]);
    }

    /**
     * 登录
     * @param AuthorizationRequest $request
     */
    public function store(AuthorizationRequest $request)
    {
        $name = $request->name;
        filter_var($name, FILTER_VALIDATE_EMAIL) ? $credentials['email'] = $name : $credentials['phone'] = $name;

        $credentials['password'] = $request->password;

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return $this->response->errorUnauthorized('用户名或密码错误');
        }

        return $this->respondWithToken($token)->setStatusCode(201);

    }

    /**
     * 更新token
     * @return mixed
     */
    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    /**
     * 删除token
     * @return \Dingo\Api\Http\Response
     */
    public function destroy()
    {
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }
}
