<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Container;

use Valit\Template;
use LogicException;

/**
 * A Container for filters.
 */
class FilterSet
{
    /**
     * @var Filter[]
     */
    protected $filters = [];

    /**
     * @var bool
     */
    protected $valueOptional = null;

    /**
     * Constructor.
     *
     * @param mixed $filters Set of filters.
     *
     * Filters can be an array such as ['required', 'isGreaterThan(100)']
     * It can also be a string (or stringable object) such as
     * 'required & isGreaterThan(100)'
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
        return $this->valueOptional = true || $this->valueOptional === null;
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
        return $this->filters;
    }

    /**
     * Turn this FilterSet into a Template.
     */
    public function template()
    {
        $template = new Template();

        foreach ($this->filters as $filter) {
            $template->addCheck($filter->name, $filter->args);
        }

        return $template;
    }

    /**
     * Normalize a set of filters and add it to $this->filters.
     *
     * Filters can be given as a string or an array.
     * When string-encoded, the string contains a number of filter expressions separated by ampersands.
     * When associative array, each key=>value pair can either be checkName => parameters
     * When numeric array, each entry contains a single filter expression.
     *
     * We normalize them into well-behaved arrays of checkName => parameters.
     *
     * @param string|array|Template $value
     */
    protected function addNormalizedFilters($value)
    {
        if ($value instanceof Template) {
            $this->filters = $value->checks;
            return;
        }

        if (!is_array($value)) {
            // turn a filter string into an array of single filter expressions.
            $value = array_map(
                function ($str) {
                    return str_replace('&&', '&', $str);
                },
                preg_split('/\s*(?<!&)&(?!&)\s*/u', (string) $value)
            );
        }

        foreach ($value as $k => $v) {
            list($checkName, $args) = $this->normalizeFilter($k, $v);

            $this->addCheck($checkName, $args);
        }
    }

    /**
     * Normalize a single filter string.
     *
     * @param int|string $key
     * @param mixed $args
     *
     * @return array containing [$checkName, $args]
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

        if (!is_string($filter)) {
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
            (array) $args,   // filter args
        ];
    }

    /**
     * Add a single filter to our array of filters.
     *
     * @param string $checkName
     * @param array  $args
     */
    public function addCheck($checkName, $args)
    {
        if (in_array($checkName, ['optional', 'required']) && $this->valueOptional !== null) {
            throw new LogicException(sprintf(
                'The value has already been marked as »%s«',
                $this->valueOptional ? 'optional' : 'required'
            ));
        }

        if ($checkName === 'optional') {
            $this->valueOptional = empty($args) || $args[0] == true;
            return;
        }

        if ($checkName === 'required') {
            $this->valueOptional = !(empty($args) || $args[0] == true);
            return;
        }

        $this->filters[] = new Filter($checkName, $args);
    }
}
