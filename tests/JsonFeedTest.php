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

    /** @test */
    public function it_only_returns_accepted_properties()
    {
        $properties = collect(app('jsonFeed')->getAcceptedProperties());
        $properties = $properties->flatMap(function ($value, $key) {
            return [
                $value => 'foo',
            ];
        })->all();

        $feed = JsonFeed::start(array_merge($properties, $arr = ['foo' => 'bar']));

        $this->assertEquals($properties, $feed->toArray());
        $this->assertNotContains($arr, $feed->toArray());
    }
}
