<?php

namespace Mateusjatenee\JsonFeed\Facades;

use Illuminate\Support\Facades\Facade;

class JsonFeed extends Facade
{
    protected function getFacadeAcessor()
    {
        return 'jsonFeed';
    }
}
