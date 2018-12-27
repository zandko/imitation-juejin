<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Cache;

class VerificationCodesController extends Controller
{
    /**
     * 手机验证码
     * @param VerificationCodeRequest $request
     * @throws \Exception
     */
    public function store(VerificationCodeRequest $request)
    {
        $phone = $request->phone;
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        if (!app()->environment('production')) {
            $code = '6379';
        } else {
            try {
                app('easysms')->send($phone, [
                    'content' => '您的验证码为：',
                    'template' => 'SMS_135033928',
                    'data' => [
                        'code' => $code,
                    ],
                ]);
            } catch (NoGatewayAvailableException $exception) {
                $message = $exception->getException('aliyun')->getMessage();
                return $this->response->errorInternal($message ?: '短信发送异常');
            }
        }

        $key = 'verificationCode_' . str_random(15);
        $expiredAt = now()->addMinutes(10);

        Cache::put($key, [
            'phone' => $phone,
            'code' => $code
        ], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expiredAt' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
