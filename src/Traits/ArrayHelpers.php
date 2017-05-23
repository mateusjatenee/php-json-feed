<?php

namespace Mateusjatenee\JsonFeed\Traits;

use Illuminate\Support\Collection;

trait ArrayHelpers
{
    /**
     * @param $items
     * @return mixed
     */
    protected function makeArray($items)
    {
        return $items instanceof Collection ? $items->all() : $items;
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param  array  $array
     * @return array
     */
    protected function collapse($array)
    {
        $results = [];

        foreach ($array as $values) {
            if ($values instanceof Collection) {
                $values = $values->all();
            } elseif (!is_array($values)) {
                continue;
            }

            $results = array_merge($results, $values);
        }

        return $results;
    }
}
