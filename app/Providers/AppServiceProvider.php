<?php

namespace App\Providers;

use Overtrue\EasySms\EasySms;
use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder as ESClientBuilder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*观察者模式*/
        \App\Models\Image::observe(\App\Observers\ImageObserver::class);
        \App\Models\Article::observe(\App\Observers\ArticleObserver::class);
        \App\Models\Reply::observe(\App\Observers\ReplyObserver::class);

        /*时间*/
        \Carbon\Carbon::setLocale('zh');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /*处理异常*/
        if (app()->isLocal()) {
            $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
        }

        /*资源未找到*/
        \API::error(function (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            abort(404);
        });

        /*无权限*/
        \API::error(function (\Illuminate\Auth\Access\AuthorizationException $exception) {
            abort(403);
        });

        /*EasySms*/
        $this->app->singleton(EasySms::class, function ($app) {
            return new EasySms(config('easysms'));
        });

        $this->app->alias(EasySms::class, 'easysms');

        /*Elasticsearch*/
        $this->app->singleton('es', function () {
            // 从配置文件读取 Elasticsearch 服务器列表
            $builder = ESClientBuilder::create()->setHosts(config('database.elasticsearch.hosts'));

            if (app()->environment() === 'local') {
                $builder->setLogger(app('log')->getMonolog());
            }

            return $builder->build();
        });
    }
}
