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

    /** @test */
    public function it_builds_a_complete_json_feed()
    {
        $feed = JsonFeed::start(
            $config = $this->getJsonFeedConfig(), $items = $this->getArrayOfItems()
        );

        $this->assertEquals($this->getExpectedJsonOutput($config, $items), $feed->toArray());
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

    protected function getArrayOfItems()
    {
        return [
            new DummyFeedItem, new DummyFeedItem,
        ];
    }

    protected function getExpectedJsonOutput($config, $items)
    {
        $config = collect($config)->filter(function ($val, $key) {
            return in_array($key, app('jsonFeed')->getAcceptedProperties());
        });

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
        }, $items);

        return $config->put('version', app('jsonFeed')->getVersion())->put('items', $items)->toArray();
    }
}
