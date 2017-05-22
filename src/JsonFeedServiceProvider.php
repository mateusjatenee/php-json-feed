<?php

namespace Mateusjatenee\JsonFeed;

use Illuminate\Support\ServiceProvider;
use Mateusjatenee\JsonFeed\JsonFeed;

class JsonFeedServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('jsonFeed', function ($app) {
            return new JsonFeed;
        });
    }
}
