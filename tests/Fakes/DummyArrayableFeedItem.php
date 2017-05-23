<?php

namespace Mateusjatenee\JsonFeed\Tests\Fakes;

use Illuminate\Contracts\Support\Arrayable;
use Mateusjatenee\JsonFeed\Tests\Fakes\DummyFeedItem;

class DummyArrayableFeedItem extends DummyFeedItem implements Arrayable
{
    public function toArray()
    {
        return ['foo' => 'bar'];
    }
}
