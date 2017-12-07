<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Util;

use Traversable;

/**
 * A Container for data to be validated.
 */
class FlattenedContainer
{
    /**
     * @var array|object
     *
     * @internal
     */
    public $container;

    /**
     * Constructor.
     */
    public function __construct($innerContainer)
    {
        $this->container = $this->flatten($innerContainer);
    }

    protected function isSimpleValue($value)
    {
        return is_scalar($value)
            || is_null($value)
            || is_resource($value)
            || $value === [];
    }

    /**
     * Expand the data available on an object.
     *
     * If an object has jsonSerialize() method, all the data returned by that method is appended
     * to the object as public properties.
     * If an object has a __debugInfo() method, all the data returned by that method is appended
     * to the object as public properties, overwriting any properties set by jsonSerialize.
     * If the object has a validationData() method, all data returned by that method is appended
     * to the object as public properties, overwriting any properties set by the previous two
     * method calls.
     *
     * @param mixed $object
     *
     * @return object;
     */
    protected function expandedValue($object)
    {
        if (!is_object($object)) {
            return $object;
        }

        $isCallable = function ($name) use ($object) {
            return method_exists($object, $name)
                && is_callable([$object, $name]);
        };

        $merge = [];

        if ($object instanceof Traversable) {
            $iteratorData = $object->iteratorData = iterator_to_array($object);
            $merge[] = $iteratorData;
            $merge[] = compact('iteratorData');
        }

        if ($isCallable('jsonSerialize')) {
            $jsonData = $object->jsonSerialize();
            $merge[] = $jsonData;
            $merge[] = compact('jsonData');
        }

        if ($isCallable('__debugInfo')) {
            $debugData = $object->__debugInfo();
            $merge[] = $debugData;
            $merge[] = compact('debugData');
        }

        if ($isCallable('validationData')) {
            $validationData = $object->validationData();
            $merge[] = $validationData;
            $merge[] = compact('validationData');
        }

        $merge[] = get_object_vars($object);

        return call_user_func_array('array_merge', $merge);
    }

    /**
     * Flatten multi dimensional array into associative array with slashes.
     *
     * @param mixed  $value
     * @param string $keyPrefix
     *
     * @return array
     */
    protected function flatten($value, $keyPrefix = '')
    {
        if ($this->isSimpleValue($value)) {
            return [$keyPrefix => $value];
        }

        $res = $keyPrefix ? [$keyPrefix => $value] : [];

        foreach ($this->expandedValue($value) as $subKey => $subValue) {
            $newKey = $keyPrefix === '' ? $subKey : "$keyPrefix/$subKey";

            $res = array_merge($res, $this->flatten($subValue, $newKey));
        }

        return $res;
    }

    /**
     * Turn a field name glob into a regular expression.
     *
     * @param string $fieldNameGlob
     *
     * @return string
     */
    protected function globToRegex($fieldNameGlob)
    {
        if ($fieldNameGlob === '*') {
            return '//';
        }

        $pathElements = explode('/', $fieldNameGlob);

        $pathRegexes = array_map(function ($element) {
            if ($element === '*') {
                return '[^/]+';
            }

            return preg_quote($element, '#');
        }, $pathElements);

        $innerRegex = implode(
            preg_quote('/', '#'),
            $pathRegexes
        );

        return sprintf('#^%s$#', $innerRegex);
    }

    /**
     * Find all the values that match the given field name glob.
     *
     * @param string $fieldNameGlob
     *
     * @return array
     */
    public function find($fieldNameGlob)
    {
        $pathRegex = $this->globToRegex($fieldNameGlob);
        $result = [];

        foreach ($this->container as $path => $val) {
            if (preg_match($pathRegex, $path)) {
                $result[$path] = $val;
            }
        }

        return $result;
    }

    /**
     * Get the debug info for this container.
     */
    public function __debugInfo()
    {
        return [
            'container' => $this->container,
        ];
    }
}
