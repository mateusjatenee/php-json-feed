<?php

namespace Mateusjatenee\JsonFeed;

use Illuminate\Support\Collection;
use Mateusjatenee\JsonFeed\Exceptions\IncorrectFeedStructureException;

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

    protected $items;

    public function __construct(array $properties = [], array $items = [])
    {
        $this->properties = new Collection($properties);
        $this->items = new Collection($items);
    }

    public static function start($properties = [])
    {
        return new static($properties);
    }

    public function toArray()
    {
        if (!$this->hasCorrectStructure()) {
            $filtered = $this->filterPropertiesies($this->properties, $this->requiredProperties)->keys()->all();

            $missingProperties = array_diff($this->requiredProperties, $filtered);

            throw (new IncorrectFeedStructureException)->setProperties($missingProperties);
        }

        return $this->filterProperties($this->properties)->all();
    }

    public function getAcceptedProperties()
    {
        return $this->acceptedProperties;
    }

    protected function hasCorrectStructure()
    {
        return $this->filterProperties($this->properties, $this->requiredProperties)->count() === count($this->requiredProperties);
    }

    protected function filterProperties(Collection $properties, $array = null)
    {
        $array = $array ?? $this->acceptedProperties;

        return $properties->filter(function ($value, $property) use ($array) {
            return in_array($property, $array);
        });
    }
}
