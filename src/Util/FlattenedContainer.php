<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Moccalotto\Valit\Util;

/**
 * A Container for data to be validated.
 */
class FlattenedContainer
{
    /**
     * @var array|object
     */
    protected $container;

    /**
     * Constructor
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
     * Flatten multi dimensional array into associative array with slashes
     *
     * @param mixed $value
     * @param string $keyPrefix
     */
    protected function flatten($value, $keyPrefix = '')
    {
        if ($this->isSimpleValue($value)) {
            return [$keyPrefix => $value];
        }

        $res = $keyPrefix ? [$keyPrefix => $value] : [];
        foreach ($value as $subKey => $subValue) {
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

        return array_filter($this->container, function ($val, $path) use ($pathRegex) {
            return preg_match($pathRegex, $path);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function __debugInfo()
    {
        return [
            'container' => $this->container
        ];
    }

}
