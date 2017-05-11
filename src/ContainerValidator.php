<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Moccalotto\Valit;

use Traversable;
use ArrayAccess;
use LogicException;

/**
 * Validate a container (variable with array access).
 */
class ContainerValidator
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var ArrayAccess|array
     */
    protected $container;

    /**
     * @var bool
     */
    protected $throwOnFailure;

    /**
     * Constructor.
     *
     * @param Manager           $manager
     * @param ArrayAccess|array $container
     * @param int               $throwOnFailure
     */
    public function __construct(Manager $manager, $container, $throwOnFailure)
    {
        $this->manager = $manager;
        $this->throwOnFailure = $throwOnFailure;
        $this->container = $container;
        $this->flatContainer = static::flatten($container);

        if (!$this->hasArrayAccess($container)) {
            throw new LogicException('$container must be an array or an object with ArrayAccess');
        }
    }

    /**
     * Check container against a number of filters.
     *
     * @param Traversable|array $containerFilters
     *
     * @return ContainerValidationResult
     */
    public function passes($containerFilters)
    {
        if (!$this->isTraversable($containerFilters)) {
            throw new LogicException('$validation must be an array or a Traversable object');
        }

        $results = [];

        foreach ($containerFilters as $fieldNameGlob => $fieldFilters) {
            $subResults =  $this->executeFilters(
                $fieldNameGlob,
                $this->normalizeFieldFilters($fieldFilters)
            );

            foreach ($subResults as $fieldPath => $fluent) {
                $results[$fieldPath] = $fluent;
            }
        }

        return new ContainerValidationResult($results);
    }

    protected static function flatten($value, $keySoFar = '')
    {
        if (!is_array($value)) {
            return [$keySoFar => $value];
        }

        $result = $keySoFar ? [$keySoFar => $value] : [];
        foreach ($value as $subKey => $subValue) {
            $newKey = $keySoFar === '' ? $subKey : "$keySoFar/$subKey";

            $result = array_merge($result, static::flatten($subValue, $newKey));
        }

        return $result;
    }

    protected function globToRegex($fieldNameGlob)
    {
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
    protected function find($fieldNameGlob)
    {
        $pathRegex = $this->globToRegex($fieldNameGlob);

        $results = [];

        foreach ($this->flatContainer as $path => $value) {
            if (preg_match($pathRegex, $path)) {
                $results[$path] = $value;
            }
        }

        return $results;
    }

    /**
     * Check if we can traverse a given variable.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function isTraversable($value)
    {
        return is_array($value)
            || (is_object($value) && ($value instanceof Traversable));
    }

    /**
     * Check if a given value is array-accessible.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function hasArrayAccess($value)
    {
        return is_array($value)
            || (is_object($value) && ($value instanceof ArrayAccess));
    }

    protected function checkRequired($filters)
    {
        if (!isset($filters['required'])) {
            return false;
        }

        if ($filters['required'] === []) {
            return true;
        }

        if (isset($filters['required'][0])) {
            return $filters['required'][0] == true;
        }

        return true;
    }

    /**
     * Execute an array of filters on a number of values.
     *
     * @param string $fieldNameGlob A field name glob (such as "address" or "order.*.id")
     * @param array $filters a normalized array of filters.
     *
     * @return array
     */
    protected function executeFilters($fieldNameGlob, array $filters)
    {
        $fieldFluent = new Fluent($this->manager, $this->container, $this->throwOnFailure);
        $fieldFluent->alias('Field');

        $results = [$fieldNameGlob => $fieldFluent];

        $values = $this->find($fieldNameGlob);

        $required = $this->checkRequired($filters);

        unset($filters['required']);

        if (empty($values)) {
            $message = $required ? '{name} is required' : '{name} is optional';
            $fieldFluent->addCustomResult(new Result(!$required, $message));

            return $results;
        }

        $results = [];

        foreach ($values as $fieldPath => $value) {
            $fluent = new Fluent($this->manager, $value, $this->throwOnFailure);
            $fluent->alias('Field');

            if ($required) {
                $fluent->addCustomResult(new Result(true, '{name} is required'));
            }

            foreach ($filters as $check => $args) {
                $fluent->__call($check, $args);
            }

            $results[$fieldPath] = $fluent;
        }

        return $results;
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
    protected function normalizeFieldFilters($filters)
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
