<?php

use Carbon\Carbon;
use Mateusjatenee\JsonFeed\FeedItem;
use Mateusjatenee\JsonFeed\Tests\Fakes\DummyFeedItem;
use Mateusjatenee\JsonFeed\Tests\TestCase;

class FeedItemTest extends TestCase
{

    /** @test */
    public function it_automatically_converts_dates_to_rfc3339()
    {
        $today = Carbon::today();
        Carbon::setTestNow($today);

        $item = new FeedItem(new DummyFeedItem);

        $this->assertEquals($today->toRfc3339String(), $item->getDatePublished());
    }

    /** @test */
    public function it_generates_an_item()
    {
        $item = new FeedItem(new DummyFeedItem);

        $expectedOutput = [
            'title' => '1984',
            'date_published' => $this->today(),
            'date_modified' => $this->today(),
            'id' => 'abc123',
            'url' => 'https://mguimaraes.co',
            'external_url' => 'https://laravel.com',
            'author' => [
                'name' => 'Mateus',
                'url' => 'https://mguimaraes.co',
            ],
            'content_html' => '<p>Great book. It\'s the best book.</p>',
            'content_text' => 'Great book. It\'s the best book.',
        ];

        $this->assertEquals($expectedOutput, $item->toArray());
    }

    protected function today()
    {
        return Carbon::today()->toRfc3339String();
    }
}
