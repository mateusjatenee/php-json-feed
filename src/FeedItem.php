<?php

namespace Mateusjatenee\JsonFeed;

use BadMethodCallException;
use Carbon\Carbon;
use Mateusjatenee\JsonFeed\Traits\ArrayHelpers;

class FeedItem
{
    use ArrayHelpers;

    /**
     * @var array
     */
    protected $requiredProperties = [
        'id',
    ];

    /**
     * @var array
     */
    protected $acceptedProperties = [
        'content_text', 'date_published', 'title', 'author', 'tags',
        'content_html', 'summary', 'image', 'banner_image',
        'id', 'url', 'external_url', 'date_modified',
    ];

    /**
     * @var array
     */
    protected $dates = ['date_published', 'date_modified'];

    /**
     * @var mixed
     */
    protected $object;

    /**
     * @var mixed
     */
    protected $attachments;

    /**
     * @param $object
     * @param array $attachments
     */
    public function __construct($object, array $attachments = [])
    {
        $this->object = $object;
        $this->attachments = $this->makeArray($attachments);
    }

    /**
     * Builds the structure of the feed item
     *
     * @return array
     */
    public function build()
    {
        return array_filter(
            $this->collapse(
                array_map(function ($property) {
                    $method = 'get' . studly_case($property);

                    return [
                        $property => $this->$method(),
                    ];
                }, $this->acceptedProperties)
            )
        );
    }

    /**
     * Converts the built item to an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->build();
    }

    /**
     * Gets a feed property if it exists
     *
     * @param string $property
     * @return mixed
     */
    public function getProperty(string $property)
    {
        $method = 'getFeed' . $property;

        $property = snake_case($property);

        if (method_exists($this->object, $method)) {
            $value = $this->object->$method();

            return in_array($property, $this->dates) ?
            (new Carbon($value))->toRfc3339String() :
            $value;
        }
    }

    /**
     * Initializes a new instance of FeedItem with the given item.
     *
     * @param array $item
     * @param array $attachments
     * @return static
     */
    public static function setItem($item = [], $attachments = [])
    {
        return new static($item, $attachments = []);
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
            return $this->getProperty(substr($method, 3));
        }

        $className = static::class;

        throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }
}
