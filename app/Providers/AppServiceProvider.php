<?php

namespace App\Providers;

use App\Http\Resources\Resource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\Models\Article::observe(\App\Observers\ArticleObserver::class);
        \App\Models\Message::observe(\App\Observers\MessageObserver::class);
        Resource::withoutWrapping();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
