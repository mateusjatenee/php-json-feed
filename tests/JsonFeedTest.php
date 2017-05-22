<?php

use Mateusjatenee\JsonFeed\JsonFeed;
use Mateusjatenee\JsonFeed\Tests\TestCase;

class JsonFeedTest extends TestCase
{
    /** @test */
    public function start_returns_an_instance_of_jsonFeed()
    {
        $this->assertInstanceOf(JsonFeed::class, JsonFeed::start());
    }
}
