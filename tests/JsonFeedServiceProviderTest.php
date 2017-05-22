<?php

use Mateusjatenee\JsonFeed\JsonFeed;
use Mateusjatenee\JsonFeed\Tests\TestCase;

class JsonFeedServiceProviderTest extends TestCase
{

    /** @test */
    public function it_is_correctly_bound_to_the_container()
    {
        $this->assertInstanceOf(JsonFeed::class, $this->app->make('jsonFeed'));
    }
}
