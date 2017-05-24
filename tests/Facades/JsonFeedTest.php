<?php

namespace Tests\Mateusjatenee\JsonFeed\Facades;

use Mateusjatenee\JsonFeed\Facades\JsonFeed as JsonFeedFacade;
use Mateusjatenee\JsonFeed\JsonFeed;
use Mateusjatenee\JsonFeed\Tests\TestCase;

class JsonFeedTest extends TestCase
{

    /** @test */
    public function it_returns_an_instance_of_json_feed()
    {
        $this->assertInstanceOf(JsonFeed::class, JsonFeedFacade::getFacadeRoot());
    }
}
