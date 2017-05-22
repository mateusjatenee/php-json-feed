<?php

namespace Mateusjatenee\JsonFeed\Exceptions;

use Exception;

class IncorrectFeedStructureException extends Exception
{
    public function setProperties(array $properties)
    {
        $this->properties = $properties;

        $props = $this->buildPropertiesString();

        $this->message = "The JSON Feed is missing the following properties: {$props}";

        return $this;
    }

    protected function buildPropertiesString()
    {
        if (count($this->properties) == 1) {
            return reset($this->properties);
        }

        $str = '';

        foreach ($this->properties as $key => $property) {
            $str .= $property . ($key == (count($this->properties) - 1) ? '' : ', ');
        }

        return $str;
    }

}
