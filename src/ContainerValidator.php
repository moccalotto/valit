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
use Moccalotto\Valit\Util\FilterSet;

/**
 * Validate a container (variable with array access).
 */
class ContainerValidator
{
    /**
     * @var Manager
     *
     * @internal
     */
    public $manager;

    /**
     * @var array|object
     *
     * @internal
     */
    public $container;

    /**
     * @var FlattenedContainer
     *
     * @internal
     */
    public $flatContainer;

    /**
     * @var bool
     *
     * @internal
     */
    public $throwOnFailure;

    /**
     * Constructor.
     *
     * @param Manager           $manager
     * @param array|object      $container
     * @param int               $throwOnFailure
     */
    public function __construct(Manager $manager, $container, $throwOnFailure)
    {
        $this->manager = $manager;
        $this->throwOnFailure = $throwOnFailure;
        $this->container = $container;
        $this->flatContainer = new Util\FlattenedContainer($container);
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
                new FilterSet($fieldFilters)
            );

            foreach ($subResults as $fieldPath => $fluent) {
                $results[$fieldPath] = $fluent;
            }
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
     * Execute an array of filters on a number of values.
     *
     * @param string    $fieldNameGlob A field name glob (such as "address" or "order.*.id")
     * @param FilterSet $filters a normalized array of filters.
     *
     * @return array
     */
    protected function executeFilters($fieldNameGlob, $filters)
    {
        $fieldFluent = new Fluent($this->manager, $this->container, $this->throwOnFailure);
        $fieldFluent->alias('Field');

        $results = [$fieldNameGlob => $fieldFluent];

        $fieldsToValidate = $this->flatContainer->find($fieldNameGlob);

        if ($fieldsToValidate === []) {
            $message = $filters->isValueRequired() ? '{name} is required' : '{name} is optional';
            $fieldFluent->addCustomResult(new Result(!$filters->isValueRequired(), $message));

            return $results;
        }

        $results = [];

        foreach ($fieldsToValidate as $fieldPath => $value) {
            $fluent = new Fluent($this->manager, $value, $this->throwOnFailure);
            $fluent->alias('Field');

            if ($filters->isValueRequired()) {
                $fluent->addCustomResult(new Result(true, '{name} is required'));
            }

            foreach ($filters->all() as $check => $args) {
                $fluent->__call($check, $args);
            }

            $results[$fieldPath] = $fluent;
        }

        return $results;
    }
}
