<?php

namespace Mateusjatenee\JsonFeed;

use BadMethodCallException;
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

    protected $version = 'https://jsonfeed.org/version/1';

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

    public function build()
    {
        if (!$this->hasCorrectStructure()) {
            $filtered = $this->filterProperties($this->properties, $this->requiredProperties)->keys()->all();

            $missingProperties = array_diff($this->requiredProperties, $filtered);

            throw (new IncorrectFeedStructureException)->setProperties($missingProperties);
        }

        $properties = $this
            ->filterProperties($this->properties)
            ->put('items', $this->buildItems()->all());

        return $properties;
    }

    public function toArray()
    {
        return $this->build()->toArray();
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

    protected function getProperty($property)
    {
        if ($this->properties->has($property)) {
            return $this->properties[$property];
        }
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function __call($method, $parameters)
    {
        if (substr($method, 0, 3) == 'get') {
            return $this->getProperty(snake_case(substr($method, 3)));
        }

        $className = static::class;

        throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }

    protected function buildItems()
    {
        return $this->items->map(function ($item) {
            return $item;
        });
    }
}
