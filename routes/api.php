<?php

use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array', 'bindings'] // 模型绑定
], function ($api) {

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function ($api) {
        /*调用频率：一分钟10次*/

        /*短信*/
        $api->post('verificationCodes', 'VerificationCodesController@store')->name('api.verificationCodes.store');

        /*注册、登录*/
        $api->post('users', 'UsersController@store')->name('api.users.store');
        $api->post('authorizations', 'AuthorizationsController@store')->name('api.authorizations.store');

        /*文章分类*/
        $api->get('categories', 'CategoriesController@index')->name('api.categories.index');

        /*文章标签*/
        $api->get('lables', 'LablesController@index')->name('api.lables.index');

        /*文章列表、文章详情*/
        $api->get('articles', 'ArticlesController@index')->name('api.articles.index');
        $api->get('articles/{article}', 'ArticlesController@show')->name('api.articles.show');

        /*某个用户的文章列表*/
        $api->get('users/{user}/articles', 'ArticlesController@userIndex')->name('api.users.articles.index');

        /*某个文章的回复列表*/
        $api->get('articles/{article}/replies', 'RepliesController@index')->name('api.replies.index');

    });

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires')
    ], function ($api) {
        /*调用频率：一分钟60次*/

        /*登录之后*/
        $api->group(['middleware' => 'api.auth'], function ($api) {

            /*用户详情、资料修改*/
            $api->get('users', 'UsersController@show')->name('api.user.show');
            $api->patch('users', 'UsersController@update')->name('api.user.update');

            /*添加文章、修改文章、删除文章*/
            $api->post('articles', 'ArticlesController@store')->name('api.articles.store');
            $api->patch('articles/{article}', 'ArticlesController@update')->name('api.articles.update');
            $api->delete('articles/{article}', 'ArticlesController@destroy')->name('api.articles.destroy');

            /*文章点赞、某个用户点赞的文章*/
            $api->patch('articles/{article}/like', 'ArticlesController@like')->name('api.articles.like');
            $api->get('users/{user}/like/articles', 'UsersController@like')->name('api.users.like.articles');

            /*收藏文章、某个用户收藏的文章*/
            $api->post('articles/{article}/collection', 'ArticlesController@collection')->name('api.articles.collection');
            $api->get('users/{user}/collection/articles', 'UsersController@collection')->name('api.users.collection.articles');

            /*文章回复、删除回复*/
            $api->post('articles/{article}/replies', 'RepliesController@store')->name('api.articles.replies.store');
            $api->delete('articles/{article}/replies/{reply}', 'RepliesController@destroy')->name('api.replies.destroy');

            /*通知消息、未读消息、清除消息*/
            $api->get('users/notifications', 'NotificationsController@index')->name('api.users.notifications.index');
            $api->get('users/notifications/stats', 'NotificationsController@stats')->name('api.users.notifications.stats');
            $api->patch('users/read/notifications', 'NotificationsController@read')->name('api.users.read.notifications');

            /*关注-取消关注、关注了、关注者*/
            $api->post('users/followed', 'FollowsController@followed')->name('api.users.followed');
            $api->get('users/{user}/following', 'FollowsController@following')->name('api.users.following');
            $api->get('users/{user}/followers', 'FollowsController@followers')->name('api.users.followers');

            /*关注标签-取消关注、关注的标签*/
            $api->post('users/follow/lables', 'LablesController@followLable')->name('api.users.follow.lables');
            $api->get('users/{user}/lables','UsersController@lable')->name('api.users.lables');

            /*上传图片（用户头像-文章封面图）*/
            $api->post('images', 'ImagesController@store')->name('api.images.store');

            /*更新token、退出登录*/
            $api->put('authorizations/current', 'AuthorizationsController@update')->name('api.authorizations.update');
            $api->delete('authorizations/current', 'AuthorizationsController@destroy')->name('api.authorizations.destroy');

        });
    });
});