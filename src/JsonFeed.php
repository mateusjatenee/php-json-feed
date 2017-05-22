<?php

namespace Mateusjatenee\JsonFeed;

use BadMethodCallException;
use Illuminate\Support\Collection;
use Mateusjatenee\JsonFeed\Exceptions\IncorrectFeedStructureException;
use Mateusjatenee\JsonFeed\FeedItem;

class JsonFeed
{
    /**
     * @var array
     */
    protected $requiredProperties = [
        'title',
    ];

    /**
     * @var array
     */
    protected $acceptedProperties = [
        'version', 'title', 'home_page_url',
        'feed_url', 'description', 'icon',
        'next_url', 'expired', 'favicon',
        'author', 'user_comment', 'hubs',
    ];

    /**
     * @var string
     */
    protected $version = 'https://jsonfeed.org/version/1';

    /**
     * @var mixed
     */
    protected $properties;

    /**
     * @var mixed
     */
    protected $items;

    /**
     * @param array $properties
     * @param array $items
     */
    public function __construct(array $properties = [], $items = [])
    {
        $this->properties = new Collection($properties);
        $this->items = $items instanceof Collection ? $items : new Collection($items);
    }

    /**
     * Returns an instance of the class
     *
     * @param array $properties
     * @return self
     */
    public static function start($properties = [], $items = [])
    {
        return new static($properties, $items);
    }

    /**
     * Builds a collection following the JSON Feed spec
     *
     * @throws \Mateusjatenee\JsonFeed\Exceptions\IncorrectFeedStructureException
     * @return \Illuminate\Support\Collection
     */
    public function build()
    {
        if (!$this->hasCorrectStructure()) {
            $missingProperties = array_diff(
                $this->requiredProperties,
                $this->filterProperties($this->requiredProperties)->keys()->all());

            throw (new IncorrectFeedStructureException)->setProperties($missingProperties);
        }

        $properties = $this
            ->filterProperties()
            ->put('version', $this->getVersion())
            ->put('items', $this->buildItems()->all());

        return $properties;
    }

    /**
     * Builds the collection and converts it to an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->build()->toArray();
    }

    /**
     * Returns an array of accepted properties
     *
     * @return array
     */
    public function getAcceptedProperties()
    {
        return $this->acceptedProperties;
    }

    public function getItems()
    {
        return $this->items;
    }

    /**
     * Checks if the properties includes the required ones
     *
     * @return boolean
     */
    protected function hasCorrectStructure()
    {
        return $this->filterProperties($this->requiredProperties)->count() === count($this->requiredProperties);
    }

    /**
     * Filter properties collection to only include accepted properties
     *
     * @param $array
     * @return mixed
     */
    protected function filterProperties($array = null)
    {
        $array = $array ?? $this->acceptedProperties;

        return $this->properties->filter(function ($value, $property) use ($array) {
            return in_array($property, $array);
        });
    }

    /**
     * Dynamically gets a property
     *
     * @param $property
     * @return string | null
     */
    protected function getProperty($property)
    {
        if ($this->properties->has($property)) {
            return $this->properties[$property];
        }
    }

    /**
     * Gets the JSON Feed version being used.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Handle dynamic methods calls
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (substr($method, 0, 3) == 'get') {
            return $this->getProperty(snake_case(substr($method, 3)));
        }

        $className = static::class;

        throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }

    /**
     * @return mixed
     */
    protected function buildItems()
    {
        return $this->items->map(function ($item) {
            return (new FeedItem($item))->toArray();
        });
    }
}
