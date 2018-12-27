<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable {
        notify as protected laravelNotify;
    }

    /**
     * 当有多个通知,自动给用户里的通知数量加1
     * @param $instance
     */
    public function notify($instance)
    {
        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }

    /**
     * 清除通知
     */
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'provider', 'provider_id'
    ];

    /**
     * JWT 接口方法
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * JWT 接口方法
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * 用户信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userInfo()
    {
        return $this->hasOne(UserInfo::class);
    }

    /**
     * 文章
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function article()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * 回复
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reply()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * 用户关注
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')
            ->withTimestamps()
            ->orderBy('follows.created_at', 'desc');
    }

    /**
     * 用户的粉丝
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')
            ->withTimestamps()
            ->orderBy('follows.created_at', 'desc');
    }

    /**
     * 关注用户
     * @param $user
     * @return array
     */
    public function followThisUser($user)
    {
        return $this->following()->toggle($user);
    }

    /**
     * 点赞的文章
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function like()
    {
        return $this->belongsToMany(Article::class, 'likes');
    }

    /**
     * 点赞
     * @param $article
     * @return array
     */
    public function likeThisArticle($article)
    {
        return $this->like()->toggle($article);
    }

    /**
     * 关注的标签
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userLable()
    {
        return $this->belongsToMany(Lable::class, 'user_lables')
            ->withTimestamps()
            ->orderBy('user_lables.created_at', 'desc');
    }

    /**
     * 关注标签
     * @param $lable
     * @return array
     */
    public function userThisLable($lable)
    {
        return $this->userLable()->toggle($lable);
    }

    /**
     * 我的收藏
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collection()
    {
        return $this->belongsToMany(Article::class, 'collections')
            ->withTimestamps()
            ->orderBy('collections.created_at', 'desc');
    }

    /**
     * 收藏
     * @param $article
     * @return array
     */
    public function userThisCollection($article)
    {
        return $this->collection()->toggle($article);
    }
}
