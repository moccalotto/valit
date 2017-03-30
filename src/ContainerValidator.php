<?php

namespace Moccalotto\Valit;

use Traversable;
use ArrayAccess;
use LogicException;

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
    public function against($containerFilters)
    {
        if (!$this->isTraversable($containerFilters)) {
            throw new LogicException('$validation must be an array or a Traversable object');
        }

        $results = [];

        foreach ($containerFilters as $fieldNameGlob => $fieldFilters) {
            $results[$fieldNameGlob] = $this->executeFilters(
                $fieldNameGlob,
                $this->normalizeFieldFilters($fieldFilters)
            );
        }

        return new ContainerValidationResult($results);
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

    /**
     * TODO Refactor.
     */
    protected function executeFilters($fieldNameGlob, array $fieldFilters)
    {
        $required = false;

        if (isset($fieldFilters['required'])) {
            $required = empty($fieldFilters['required']) ? false : !$fieldFilters['required'][0];

            unset($fieldFilters['required']);
        }

        if ($required && !isset($this->container[$fieldNameGlob])) {
            return (new Fluent($this->manager, $this->container, $this->throwOnFailure))
                ->alias('container')
                ->addCustomResult(new Result(false, 'Field {0} must exist in {name}', [$fieldNameGlob]));
        }

        if (!isset($this->container[$fieldNameGlob])) {
            return (new Fluent($this->manager, $this->container, $this->throwOnFailure))
                ->alias('container')
                ->addCustomResult(new Result(true, 'Field {0} is optional in {name}', [$fieldNameGlob]));
        }

        $fluent = new Fluent($this->manager, $this->container[$fieldNameGlob], $this->throwOnFailure);
        $fluent->alias('Field value');

        if ($required) {
            $fluent->addCustomResult(new Result(true, 'Field {0} must exist in {name}', [$fieldNameGlob]));
        }

        foreach ($fieldFilters as $check => $args) {
            $fluent->__call($check, $args);
        }

        return $fluent;
    }

    /**
     * Normalize a set of filters.
     *
     * Filters can be given as a string or an array.
     * When string-encoded, the string contains a number of "function call" expressions,
     * separated by ampersands.
     * When array-encoded, each key=>value pair can either be filterName => parameters
     * or notUsed => filterStringToBeParsed
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
            $filters = preg_split('/\s*(?<!&)&(?!&)\s*/u', (string) $filters);
        }

        $result = [];

        foreach ($filters as $check => $args) {
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
