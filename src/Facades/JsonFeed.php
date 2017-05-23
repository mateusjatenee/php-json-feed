<?php

namespace Mateusjatenee\JsonFeed\Facades;

use Illuminate\Support\Facades\Facade;

class JsonFeed extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'jsonFeed';
    }
}
