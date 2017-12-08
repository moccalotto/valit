<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Container;

use LogicException;

/**
 * A Container for filters.
 */
class FilterSet
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var bool
     */
    protected $valueOptional = false;

    /**
     * @var bool
     */
    protected $optionalityDefined = false;

    /**
     * Constructor.
     */
    public function __construct($filters)
    {
        $this->addNormalizedFilters($filters);
    }

    /**
     * Is this value required (i.e. the field is present) ?
     *
     * If it is required the field must be
     * present and the value must pass all
     * filters.
     *
     * @return bool
     */
    public function isValueRequired()
    {
        return ! $this->valueOptional;
    }

    /**
     * Is this value optional (i.e. the field need not be present) ?
     *
     * If it is optional, the filters need not pass
     * if the value is not present.
     *
     * @return bool
     */
    public function isValueOptional()
    {
        return $this->valueOptional;
    }

    /**
     * Get all filters (except the "required" and "optional" pseudo-filters).
     *
     * @return array an array of normalized filters
     */
    public function all()
    {
        $result = [];

        foreach ($this->filters as $key => $val) {
            if ($key === 'optional') {
                continue;
            }
            if ($key === 'required') {
                continue;
            }

            $result[$key] = $val;
        }

        return $result;
    }

    /**
     * Normalize a set of filters and add it to $this->filters
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
     * @return void
     */
    protected function addNormalizedFilters($filters)
    {
        if (!is_array($filters)) {
            // turn a filter string into an array of single filter expressions.
            $filters = preg_split('/\s*(?<!&)&(?!&)\s*/u', (string) $filters);
        }

        foreach ($filters as $key => $args) {
            list($filterName, $args) = $this->normalizeFilter($key, $args);

            $this->addFilter($filterName, $args);
        }
    }

    /**
     * Normalize a single filter string.
     */
    protected function normalizeFilter($key, $args)
    {
        if (is_int($key) && is_string($args)) {
            // Example:
            // $key: 0
            // $args: "isGreaterThan(0)"
            $filter = $args;
        } elseif (is_int($key) && is_array($args)) {
            // Example 1:
            // $key: 42
            // $args: ["isGreaterThan" => [0]]
            //
            // Example 2:
            // $key: 1987
            // $args: ["isGreaterThan(0)"]
            $filter = array_shift($args);
        } else {
            // Example:
            // $key:  "isGreaterThan"
            // $args: [0]
            $filter = $key;
        }

        if (! is_string($filter)) {
            throw new LogicException(sprintf('Invalid filter at index %d', $key));
        }

        if (!preg_match('/([a-z0-9]+)\s*(?:\((.*?)\))?$/Aui', $filter, $matches)) {
            throw new LogicException(sprintf('Invalid filter »%s«', $filter));
        }

        if (isset($matches[2])) {
            $args = json_decode(sprintf('[%s]', $matches[2]));
        }

        return [
            $matches[1],    // filter name
            (array) $args   // filter args
        ];
    }

    /**
     * Add a single filter to our array of filters.
     *
     * @param string $filterName
     * @param array  $args
     */
    protected function addFilter($filterName, $args)
    {
        if (in_array($filterName, ['optional', 'required']) && $this->optionalityDefined) {
            throw new LogicException('A set of filters cannot be both optional and required!');
        }

        if ($filterName === 'optional') {
            $this->valueOptional = empty($args) || $args[0] == true;
            $this->optionalityDefined = true;

            return;
        }

        if ($filterName === 'required') {
            $this->valueOptional = ! (empty($args) || $args[0] == true);
            $this->optionalityDefined = true;

            return;
        }

        $this->filters[] = new Filter($filterName, $args);
    }
}
