<?php

namespace Mateusjatenee\JsonFeed;

use Illuminate\Support\Collection;
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
        $config = config('laravel-json-feed');

        $this->app->singleton('jsonFeed', function ($app) use ($config) {
            return new JsonFeed($config);
        });

        Collection::macro('intersectByKeys', function ($array) {
            return new static(array_intersect_key($this->items, $array));
        });
    }
}
