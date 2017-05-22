<?php

namespace Mateusjatenee\JsonFeed;

use Illuminate\Support\ServiceProvider;
use Mateusjatenee\JsonFeed\JsonFeed;

class JsonFeedServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-json-feed.php' => config_path('laravel-json-feed.php'),
        ], 'config');
    }

    public function register()
    {
        $this->app->bind('jsonFeed', function ($app) {
            return new JsonFeed;
        });
    }
}
