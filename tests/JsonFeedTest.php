<?php

use Illuminate\Support\Collection;
use Mateusjatenee\JsonFeed\Exceptions\IncorrectFeedStructureException;
use Mateusjatenee\JsonFeed\JsonFeed;
use Mateusjatenee\JsonFeed\Tests\Fakes\DummyFeedItem;
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

        $properties['version'] = app('jsonFeed')->getVersion();

        $feed = JsonFeed::start(
            array_merge($properties, $arr = ['foo' => 'bar'])
        );

        $this->assertArraySubset($properties, $feed->toArray());
        $this->assertNotContains($arr, $feed->toArray());
    }

    /** @test */
    public function it_throws_an_exception_if_the_feed_does_not_contain_required_properties()
    {
        $properties = [
            'description' => 'foo bar',
        ];

        $this->expectException(IncorrectFeedStructureException::class);
        $this->expectExceptionMessage('The JSON Feed is missing the following properties: title');

        JsonFeed::start($properties)->toArray();
    }

    /** @test */
    public function it_throws_an_exception_if_the_feed_does_not_contain_at_least_one_required_property()
    {
        $properties = [
            'version' => 'foo',
            'description' => 'bar',
        ];

        $this->expectException(IncorrectFeedStructureException::class);
        $this->expectExceptionMessage('The JSON Feed is missing the following properties: title');

        JsonFeed::start($properties)->toArray();
    }

    /** @test */
    public function it_automatically_sets_the_version()
    {
        $this->assertEquals('https://jsonfeed.org/version/1', JsonFeed::start()->getVersion());
    }

    /** @test */
    public function it_automatically_gets_properties()
    {
        $feed = JsonFeed::start([
            'description' => 'bar',
            'home_page_url' => 'https://google.com',
        ]);

        $this->assertEquals('https://google.com', $feed->getHomePageUrl());
        $this->assertEquals('bar', $feed->getDescription());
    }

    /** @test */
    public function it_automatically_converts_an_array_to_a_collection()
    {
        $feed = JsonFeed::start([], [new DummyFeedItem]);
        $this->assertInstanceOf(Collection::class, $feed->getItems());
    }

    /** @test */
    public function it_does_not_add_a_collection_inside_another_collection()
    {
        $feed = JsonFeed::start([], collect([new DummyFeedItem]));

        $this->assertInstanceOf(Collection::class, $feed->getItems());
        $this->assertInstanceOf(DummyFeedItem::class, $feed->getItems()->first());
    }
}
