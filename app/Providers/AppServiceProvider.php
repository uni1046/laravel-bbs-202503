<?php

namespace App\Providers;

use App\Listeners\EmailVerified;
use App\Models\Topic;
use App\Observers\TopicObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Event::listen(
            EmailVerified::class, // 事件类, 在用户完成邮箱验证后触发
        );

        Topic::observe(TopicObserver::class);

        // 使用 Bootstrap 样式的分页器
        Paginator::useBootstrap();
    }
}
