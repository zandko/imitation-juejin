<?php

namespace App\Http\Controllers\Api;

use App\Transformers\NotificationTransformer;
use Auth;

class NotificationsController extends Controller
{
    /**
     * 消息通知列表
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        $notifications = Auth::guard('api')->user()->notifications()->paginate(10);

        return $this->response->paginator($notifications, new NotificationTransformer());
    }

    /**
     * 未读消息
     * @return mixed
     */
    public function stats()
    {
        return $this->response->array([
            'unread_count' => Auth::guard('api')->user()->notification_count,
        ]);
    }

    /**
     * 清除通知
     */
    public function read()
    {
        Auth::guard('api')->user()->markAsRead();
        return $this->response->noContent();
    }
}
