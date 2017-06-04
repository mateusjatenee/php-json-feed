<?php

namespace Mateusjatenee\JsonFeed;

use Carbon\Carbon;
use Mateusjatenee\JsonFeed\Traits\ArrayHelpers;
use Mateusjatenee\JsonFeed\Traits\ItemGetters;

class FeedItem
{
    use ArrayHelpers, ItemGetters;

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
            $this->flatMap($this->acceptedProperties, function ($property) {
                $method = 'get' . studly_case($property);

                return [$property => $this->$method()];
            })
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
}
