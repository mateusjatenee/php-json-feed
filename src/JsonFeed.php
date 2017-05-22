<?php

namespace Mateusjatenee\JsonFeed;

use Illuminate\Support\Collection;

class JsonFeed
{
    protected $requiredProperties = [
        'version', 'title',
    ];

    protected $acceptedProperties = [
        'version', 'title', 'home_page_url',
        'feed_url', 'description', 'icon',
        'next_url', 'expired', 'favicon',
        'author', 'user_comment', 'hubs',
    ];

    protected $properties;

    public function __construct(array $properties = [])
    {
        $this->properties = new Collection($properties);
    }

    public static function start($properties = [])
    {
        return new static($properties);
    }

    public function toArray()
    {
        return $this->filterProperties($this->properties)->all();
    }

    public function getAcceptedProperties()
    {
        return $this->acceptedProperties;
    }

    protected function filterProperties(Collection $properties)
    {
        return $properties->filter(function ($value, $property) {
            return in_array($property, $this->acceptedProperties);
        });
    }
}
