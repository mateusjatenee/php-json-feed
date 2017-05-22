<?php

namespace Mateusjatenee\JsonFeed\Tests;

use Mateusjatenee\JsonFeed\JsonFeedServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function getPackageProviders($app)
    {
        return [JsonFeedServiceProvider::class];
    }
}
