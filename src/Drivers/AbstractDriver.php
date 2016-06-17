<?php namespace Znck\Livre\Drivers;

use Znck\Livre\Contracts\BookSearchDriver;

abstract class AbstractDriver implements BookSearchDriver
{
    protected static $queryTransformationMap = [];

    protected static $bookTransformationMap = [];

    /**
     * Transform request parameters using the provided map.
     *
     * @param $query
     * @param $map
     *
     * @return array
     */
    protected function transformQueryParameters($query, $map)
    {
        $processed = [];
        $ignored = [];
        foreach ((array)$query as $key => $value) {
            if (! empty($value)) {
                if (array_key_exists($key, $map)) {
                    $processed[$map[$key]] = $value;
                } else {
                    $ignored[$key] = $value;
                }
            }
        }

        return [$processed, $ignored];
    }

    protected function transformBookAttributes(array $attributes, array $map)
    {
        $processed = $attributes;

        foreach (array_dot($map) as $key => $replace) {
            $value = array_get($processed, $key);
            if (! empty($value)) {
                $processed[$replace] = $value;
                array_forget($processed, $key);
            }
        }

        return $processed;
    }

    /**
     * @return array
     */
    protected function queryTransformationMap()
    {
        return static::$queryTransformationMap;
    }

    /**
     * @return array
     */
    protected function bookTransformationMap()
    {
        return static::$bookTransformationMap;
    }
}
