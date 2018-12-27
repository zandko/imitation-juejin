1、从终端运行Composer require命令：

```php
composer require overtrue/easy-sms
项目中用到的版本："overtrue/easy-sms": "^1.1"
```

2、在`config`目录中添加配置文件 `easysms.php`：

```php
<?php

return [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [
            'yunpian', 'aliyun',
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'errorlog' => [
            'file' => '/tmp/easy-sms.log',
        ],
        'yunpian' => [
            'api_key' => env('YUNPIAN_API_KEY'),
        ],
        'aliyun' => [
            'access_key_id' => env('ALIYUN_KEY_ID'),
            'access_key_secret' => env('ALIYUN_KEY_SECRET'),
            'sign_name' => env('ALIYUN_NAME'),
        ],
    ],
];
```

3、在 `.env` 文件中配置

```php
# 云片
YUNPIAN_API_KEY=

# 阿里云
ALIYUN_KEY_ID=
ALIYUN_KEY_SECRET=
ALIYUN_NAME=
```

4、注入容器（服务提供者）

```php
$this->app->singleton(EasySms::class, function ($app) {
     return new EasySms(config('easysms'));
});

$this->app->alias(EasySms::class, 'easysms');
```

5、创建 `VerificationCodeRequest`

```php
<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class VerificationCodeRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => [
                'required',
                'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/',
                'unique:users'
            ]
        ];
    }
}

```

6、实际业务代码 `VerificationCodesController.php`

```php
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
```

