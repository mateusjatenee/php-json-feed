<?php

namespace Mateusjatenee\JsonFeed;

use BadMethodCallException;
use Illuminate\Support\Collection;
use Mateusjatenee\JsonFeed\Exceptions\IncorrectFeedStructureException;
use Mateusjatenee\JsonFeed\FeedItem;
use Mateusjatenee\JsonFeed\Traits\ArrayHelpers;

class JsonFeed
{
    use ArrayHelpers;

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
        $this->properties = $this->makeArray($properties);
        $this->items = $this->makeArray($items);
    }

    /**
     * Returns an instance of the class
     *
     * @param array $properties
     * @return static
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
            throw (new IncorrectFeedStructureException)->setProperties($this->getMissingProperties());
        }

        return $this
            ->filterProperties() + ['version' => $this->getVersion(), 'items' => $this->buildItems()];
    }

    /**
     * Builds the collection and converts it to an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->build();
    }

    public function toJson()
    {
        return json_encode($this->build());
    }

    /**
     * Set the feed's items
     *
     * @param $items
     * @return self
     */
    public function setItems($items)
    {
        $this->items = $this->makeArray($items);

        return $this;
    }

    /**
     * @param $config
     * @return self
     */
    public function setConfig($config)
    {
        $this->properties = $this->makeArray($config);

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
     * Gets the Json Feed config
     *
     * @return \Illuminate\Support\Collection
     */
    public function getConfig()
    {
        return $this->properties;
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
     * Dynamically gets a property
     *
     * @param $property
     * @return string | null
     */
    protected function getProperty($property)
    {
        return $this->properties[$property] ?? null;
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
     * Gets the missing properties in case the JSON feed is not avlid
     *
     * @return array the missing required properties
     */
    protected function getMissingProperties()
    {
        return array_diff(
            $this->requiredProperties,
            array_keys($this->filterProperties($this->requiredProperties))
        );
    }

    /**
     * Checks if the properties includes the required ones
     *
     * @return boolean
     */
    protected function hasCorrectStructure()
    {
        return count($this->filterProperties($this->requiredProperties)) === count($this->requiredProperties);
    }

    /**
     * Filter properties collection to only include accepted properties
     *
     * @param $array
     * @return mixed
     */
    protected function filterProperties($array = null)
    {
        return array_intersect_key($this->properties, array_flip($array ?? $this->acceptedProperties));
    }

    /**
     * @return mixed
     */
    protected function buildItems()
    {
        return array_map(function ($item) {
            return FeedItem::setItem($item)->toArray();
        }, $this->items);
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
