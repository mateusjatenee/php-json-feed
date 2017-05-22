<?php

namespace Mateusjatenee\JsonFeed;

use Illuminate\Support\Collection;

class JsonFeed
{
    protected $requiredProperties = [
        'id',
    ];

    protected $acceptedProperties = [
        'content_text', 'date_published', 'title', 'author', 'tags',
        'content_html', 'summary', 'image', 'banner_image',
        'id', 'url', 'external_url', 'date_modified',
    ];

    protected $object;

    protected $attachments;

    public function __construct($object, array $attachments = [])
    {
        $this->object = $object;
        $this->attachments = new Collection($attachments);
    }

    public function build()
    {
    }

    public function toArray()
    {
    }
}
