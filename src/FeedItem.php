<?php

namespace Mateusjatenee\JsonFeed;

use BadMethodCallException;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FeedItem
{
    protected $requiredProperties = [
        'id',
    ];

    protected $acceptedProperties = [
        'content_text', 'date_published', 'title', 'author', 'tags',
        'content_html', 'summary', 'image', 'banner_image',
        'id', 'url', 'external_url', 'date_modified',
    ];

    protected $dates = ['date_published', 'date_modified'];

    protected $object;

    protected $attachments;

    public function __construct($object, array $attachments = [])
    {
        $this->object = $object;
        $this->attachments = new Collection($attachments);
    }

    public function build()
    {
        return (new Collection($this->acceptedProperties))->flatMap(function ($property) {
            $method = 'get' . camel_case($property);

            return [
                $property => $this->$method(),
            ];
        })->reject(function ($value, $property) {
            return empty($value);
        });
    }

    public function toArray()
    {
        return $this->build()->toArray();
    }

    public function getProperty(string $property)
    {
        $method = 'getFeed' . studly_case($property);

        if (method_exists($this->object, $method)) {
            $value = $this->object->$method();

            return in_array($property, $this->dates) ?
            (new Carbon($value))->toRfc3339String() :
            $value;
        }
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
