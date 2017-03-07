<?php

namespace Moccalotto\Valit;

use Traversable;
use ArrayAccess;
use LogicException;
use Moccalotto\Valit\Result;
use Moccalotto\Valit\Manager;

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
     * Constructor
     *
     * @param Manager $manager
     * @param ArrayAccess|array $container
     * @param int $throwOnFailure
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

        foreach ($containerFilters as $field => $fieldFilters) {
            $results[$field] = $this->executeFilters(
                $field,
                $this->normalizeFilters($fieldFilters)
            );
        }

        return new ContainerValidationResult($results);
    }

    protected function isTraversable($value)
    {
        return is_array($value)
            || (is_object($value) && ($value instanceof Traversable));
    }

    protected function hasArrayAccess($value)
    {
        return is_array($value)
            || (is_object($value) && ($value instanceof ArrayAccess));
    }

    /**
     * TODO Refactor.
     */
    protected function executeFilters($field, array $fieldFilters)
    {
        $required = false;

        if (isset($fieldFilters['required'])) {
            $required = empty($fieldFilters['required']) ? true : !$fieldFilters['required'][0];

            unset($fieldFilters['required']);
        }

        if ($required && !isset($this->container[$field])) {
            return (new Fluent($this->manager, $this->container, $this->throwOnFailure))
                ->alias($field)
                ->addCustomResult(new Result(false, '{name} is required'));
        }

        if (!isset($this->container[$field])) {
            return (new Fluent($this->manager, $this->container, $this->throwOnFailure))
                ->alias($field)
                ->addCustomResult(new Result(true, '{name} is optional'));
        }

        $fluent = new Fluent($this->manager, $this->container[$field], $this->throwOnFailure);
        $fluent->alias($field);

        if ($required) {
            $fluent->addCustomResult(new Result(true, '{name} is required'));
        } else {
            $fluent->addCustomResult(new Result(true, '{name} is optional'));
        }

        foreach ($fieldFilters as $check => $args) {
            $fluent->__call($check, $args);
        }

        return $fluent;
    }

    protected function normalizeFilters($filters)
    {
        if (!is_array($filters)) {
            $filters = preg_split('/\s*(?<!&)&(?!&)\s*/u', $filters);
        }
        foreach ($filters as $check => $args) {
            if (is_int($check)) {
                $check = $args;
                $args = [];
            }

            if (!is_array($args)) {
                $args = [$args];
            }

            if (!preg_match('/([a-z0-9]+)\s*(?:\((.*?)\))?$/Aui', $check, $matches)) {
                throw new LogicException(sprintf('Invalid filter »%s«', $check));
            }

            $check = $matches[1];

            if (isset($matches[2])) {
                $args = json_decode(sprintf('[%s]', $matches[2]));
            }

            $result[$check] = $args;
        }

        return $result;
    }
}
