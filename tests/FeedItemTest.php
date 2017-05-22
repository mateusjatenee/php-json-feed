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

        $item = new FeedItem(new DummyFeedItem);

        $this->assertEquals($today->toRfc3339String(), $item->getDatePublished());
    }
}
