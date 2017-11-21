<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Moccalotto\Valit\Util;

use LogicException;

/**
 * A Container for filters.
 */
class FilterSet
{
    /**
     * @var array
     */
    protected $filters;

    /**
     * @var bool
     */
    protected $valueRequired = false;

    /**
     * Constructor.
     */
    public function __construct($filters)
    {
        $this->filters = $this->normalize($filters);
    }

    public function isValueRequired()
    {
        return $this->valueRequired;
    }

    /**
     * Get all filters (except the "required" filter).
     *
     * @return array an array of normalized filters
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function all()
    {
        $result = [];

        foreach ($this->filters as $key => $val) {
            if ($key !== 'required') {
                $result[$key] = $val;
            }
        }

        return $result;
    }

    /**
     * Normalize a set of filters.
     *
     * Filters can be given as a string or an array.
     * When string-encoded, the string contains a number of filter expressions separated by ampersands.
     * When associative array, each key=>value pair can either be filterName => parameters
     * When numeric array, each entry contains a single filter expression.
     *
     * We normalize them into well-behaved arrays of filterName => parameters.
     *
     * @param string|array $filters
     *
     * @return array
     */
    protected function normalize($filters)
    {
        if (!is_array($filters)) {
            // turn a filter string into an array of single filter expressions.
            $filters = preg_split('/\s*(?<!&)&(?!&)\s*/u', (string) $filters);
        }

        $result = [];

        foreach ($filters as $check => $args) {
            // we handle numeric arrays differently from assoc arrays.
            if (is_int($check) && is_string($args)) {
                $check = $args;
                $args = [];
            } elseif (is_int($check) && is_array($args)) {
                $check = array_shift($args);
            } elseif (is_int($check)) {
                throw new LogicException(sprintf('Invalid filter at index %d', $check));
            }

            if (!preg_match('/([a-z0-9]+)\s*(?:\((.*?)\))?$/Aui', $check, $matches)) {
                throw new LogicException(sprintf('Invalid filter »%s«', $check));
            }

            $check = $matches[1];

            if (isset($matches[2])) {
                $args = (array) json_decode(sprintf('[%s]', $matches[2]));
            }

            if ($check === 'required') {
                $this->valueRequired = empty($args) || $args[0] == true;
                continue;
            }

            $result[] = new Filter($check, (array) $args);
        }

        return $result;
    }
}
