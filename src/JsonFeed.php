<?php

namespace Mateusjatenee\JsonFeed;

class JsonFeed
{
    protected $requiredProperties = [
        'version', 'title',
    ];

    protected $properties;

    public function __construct(array $properties = [])
    {
        $this->properties = $properties;
    }

    public static function start($properties = [])
    {
        return new static($properties);
    }
}
