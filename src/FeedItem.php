<?php

namespace Mateusjatenee\JsonFeed;

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

                $method = $this->getMethodForProperty($property);

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
