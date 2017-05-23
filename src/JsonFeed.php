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
    public function __construct($properties = [], $items = [])
    {
        $this->properties = $this->makeCollection($properties);
        $this->items = $this->makeCollection($items);
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
                $this->filterProperties($this->requiredProperties)->keys()->all()
            );

            throw (new IncorrectFeedStructureException)->setProperties($missingProperties);
        }

        return $this
            ->filterProperties()
            ->put('version', $this->getVersion())
            ->put('items', $this->buildItems()->all());
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

    public function toJson()
    {
        return $this->build()->toJson();
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

    /**
     * Set the feed's items
     *
     * @param $items
     * @return self
     */
    public function setItems($items)
    {
        $this->items = $this->makeCollection($items);

        return $this;
    }

    /**
     * Gets the feed items
     *
     * @return \Illuminate\Support\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param $config
     * @return self
     */
    public function setConfig($config)
    {
        $this->properties = $this->makeCollection($config);

        return $this;
    }

    /**
     * Gets the Json Feed config
     *
     * @return \Illuminate\Support\Collection
     */
    public function getConfig()
    {
        return $this->properties;
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
        return $this->properties->intersectByKeys(array_flip($array ?? $this->acceptedProperties));
    }

    /**
     * Dynamically gets a property
     *
     * @param $property
     * @return string | null
     */
    protected function getProperty($property)
    {
        return $this->properties->get($property);
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
     * @param $items
     * @return mixed
     */
    protected function makeCollection($items)
    {
        return $items instanceof Collection ? $items : new Collection($items);
    }

    /**
     * @return mixed
     */
    protected function buildItems()
    {
        return $this->items->map(function ($item) {
            return FeedItem::setItem($item)->toArray();
        });
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
}
