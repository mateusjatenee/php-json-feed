<?php

namespace Mateusjatenee\JsonFeed\Tests\Fakes;

use Carbon\Carbon;
use Mateusjatenee\JsonFeed\Contracts\FeedItemContract;

class DummyFeedItem implements FeedItemContract
{
    protected $url = 'https://mguimaraes.co';

    public function getFeedTitle()
    {
        return (string) 1984;
    }

    public function getFeedDatePublished()
    {
        return Carbon::today();
    }

    public function getFeedDateModified()
    {
        return Carbon::today();
    }

    public function getFeedId()
    {
        return 'abc123';
    }

    public function getFeedUrl()
    {
        return $this->url;
    }

    public function getFeedExternalUrl()
    {
        return 'https://laravel.com';
    }

    public function getFeedAuthor()
    {
        return [
            'name' => 'Mateus',
            'url' => $this->url,
        ];
    }

    public function getFeedContentHtml()
    {
        return '<p>Great book. It\'s the best book.</p>';
    }

    public function getFeedContentText()
    {
        return 'Great book. It\'s the best book.';
    }
}
