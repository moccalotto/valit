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

use LogicException;

/**
 * A Container for filters
 */
class FilterSet
{
    /**
     * @var array
     */
    protected $filters;

    /**
     * Constructor
     */
    public function __construct($filters)
    {
        $this->filters = $this->normalize($filters);
    }

    public function isValueRequired()
    {
        if (!isset($this->filters['required'])) {
            return false;
        }

        if ($this->filters['required'] === []) {
            return true;
        }

        if (isset($this->filters['required'][0])) {
            return $this->filters['required'][0] == true;
        }

        return true;
    }

    /**
     * Get all filters (except the "required" filter)
     *
     * @return array an array of normalized filters
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function all()
    {
        return array_filter($this->filters, function ($val, $key) {
            return $key !== 'required';
        }, ARRAY_FILTER_USE_BOTH);
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
            if (is_int($check)) {
                $check = $args;
                $args = [];
            }

            if (!preg_match('/([a-z0-9]+)\s*(?:\((.*?)\))?$/Aui', $check, $matches)) {
                throw new LogicException(sprintf('Invalid filter »%s«', $check));
            }

            $check = $matches[1];

            if (isset($matches[2])) {
                $args = json_decode(sprintf('[%s]', $matches[2]));
            }

            $result[$check] = (array) $args;
        }

        return $result;
    }
}
