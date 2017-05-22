<?php

namespace Mateusjatenee\JsonFeed\Tests\Fakes;

use Carbon\Carbon;

class DummyFeedItem
{
    public function getFeedDatePublished()
    {
        return Carbon::today();
    }
}
