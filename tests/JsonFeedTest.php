<?php

use Illuminate\Support\Collection;
use Mateusjatenee\JsonFeed\Exceptions\IncorrectFeedStructureException;
use Mateusjatenee\JsonFeed\JsonFeed;
use Mateusjatenee\JsonFeed\Tests\Fakes\DummyArrayableFeedItem;
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
    public function it_automatically_converts_a_collection_to_array()
    {
        $feed = JsonFeed::start(new Collection([]), new Collection([new DummyFeedItem]));

        $this->assertInternalType('array', $feed->getItems());
        $this->assertInternalType('array', $feed->getConfig());
    }

    /** @test */
    public function it_does_not_add_a_collection_inside_another_collection()
    {
        $feed = JsonFeed::start([], collect([new DummyFeedItem]));

        $this->assertInstanceOf(DummyFeedItem::class, $feed->getItems()[0]);
    }

    /** @test */
    public function it_sets_the_config()
    {
        $feed = app('jsonFeed')->setConfig($config = $this->getJsonFeedConfig());

        $this->assertEquals(count($config), count($feed->getConfig()));
    }

    /** @test */
    public function it_sets_the_items()
    {
        $feed = app('jsonFeed')->setItems($items = $this->getArrayOfItems());

        $this->assertEquals(count($items), count($feed->getItems()));
    }

    /** @test */
    public function it_builds_a_complete_json_feed()
    {
        $feed = JsonFeed::start(
            $config = $this->getJsonFeedConfig(), $items = $this->getArrayOfItems()
        );

        $this->assertEquals($expected = $this->getExpectedJsonOutput($config, $items), $feed->toArray());
        $this->assertEquals($expected, json_decode($feed->toJson(), true));
    }

    /** @test */
    public function it_builds_a_complete_json_feed_from_an_arrayable_collection()
    {
        $feed = JsonFeed::start(
            $config = $this->getJsonFeedConfig(), $items = collect($this->getArrayOfItems(true))
        );

        $this->assertEquals($expected = $this->getExpectedJsonOutput($config, $items), $feed->toArray());
        $this->assertEquals($expected, json_decode($feed->toJson(), true));
    }

    protected function getJsonFeedConfig()
    {
        return [
            'title' => 'My JSON Feed test',
            'home_page_url' => 'https://mguimaraes.co',
            'should_not_appear' => 'foobar',
            'feed_url' => 'https://mguimaraes.co/feeds/json',
            'author' => [
                'url' => 'https://twitter.com/mateusjatenee',
                'name' => 'Mateus Guimarães',
            ],
            'icon' => 'https://mguimaraes.co/assets/img/icons/apple-touch-icon-72x72.png',
            'favicon' => 'https://mguimaraes.co/assets/img/icons/favicon.ico',
        ];
    }

    protected function getArrayOfItems($arrayable = false)
    {
        $model = $arrayable ? DummyArrayableFeedItem::class : DummyFeedItem::class;

        return [
            new $model, new $model,
        ];
    }

    protected function getExpectedJsonOutput($config, $items)
    {
        $config = array_intersect_key($config, array_flip(app('jsonFeed')->getAcceptedProperties()));

        $items = array_map(function ($item) {
            return [
                'title' => $item->getFeedTitle(),
                'date_published' => $item->getFeedDatePublished()->toRfc3339String(),
                'date_modified' => $item->getFeedDateModified()->toRfc3339String(),
                'id' => $item->getFeedId(),
                'url' => $item->getFeedUrl(),
                'external_url' => $item->getFeedExternalUrl(),
                'author' => $item->getFeedAuthor(),
                'content_html' => $item->getFeedContentHtml(),
                'content_text' => $item->getFeedContentText(),
            ];
        }, $items instanceof Collection ? $items->all() : $items);

        return $config + ['version' => app('jsonFeed')->getVersion(), 'items' => $items];
    }
}
