<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Container;

use Traversable;
use Valit\Fluent;
use Valit\Result;
use Valit\Manager;
use Valit\Template;
use LogicException;
use BadMethodCallException;
use Valit\Assertion\AssertionNormalizer;

/**
 * Validate a container (variable with array access).
 */
class Validator
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
     * @var string
     */
    public $alias = 'container';

    /**
     * Constructor.
     *
     * @param Manager      $manager
     * @param array|object $container
     * @param int          $throwOnFailure
     */
    public function __construct(Manager $manager, $container, $throwOnFailure)
    {
        $this->manager = $manager;
        $this->throwOnFailure = $throwOnFailure;
        $this->container = $container;
        $this->flatContainer = new FlattenedContainer($container);
    }

    /**
     * Check container against a number of assertions.
     *
     * @param Traversable|array $containerAssertionMap
     *
     * @return ContainerValidationResult
     */
    public function passes($containerAssertionMap)
    {
        if (!$this->isTraversable($containerAssertionMap)) {
            throw new LogicException('$validation must be an array or a Traversable object');
        }

        $results = [];

        foreach ($containerAssertionMap as $fieldNameGlob => $assertions) {
            $subResults = $this->executeAssertions(
                $fieldNameGlob,
                (new AssertionNormalizer($assertions))->assertions
            );

            foreach ($subResults as $fieldPath => $fluent) {
                $results[$fieldPath] = $fluent;
            }
        }

        return new ValidationResult($results, $this->alias);
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
     * Execute an array of assertions on a number of values.
     *
     * @param string       $fieldNameGlob A field name glob (such as "address" or "order.*.id")
     * @param AssertionBag $assertions    A normalized array of assertions
     *
     * @return array
     */
    protected function executeAssertions($fieldNameGlob, $assertions)
    {
        $fieldFluent = new Fluent($this->manager, $this->container, $this->throwOnFailure);
        $fieldFluent->alias($fieldNameGlob);

        $results = [$fieldNameGlob => $fieldFluent];

        $fieldsToValidate = $this->flatContainer->find($fieldNameGlob);

        if ($fieldsToValidate === []) {
            $message = $assertions->isOptional() ? '{name} is optional' : '{name} must be present';
            $fieldFluent->addCustomResult(new Result($assertions->isOptional(), $message));

            return $results;
        }

        $results = [];

        foreach ($fieldsToValidate as $fieldPath => $value) {
            $fluent = new Fluent($this->manager, $value, $this->throwOnFailure);
            $fluent->alias($fieldPath);

            if (!$assertions->isOptional()) {
                $fluent->addCustomResult(new Result(true, '{name} must be present'));
            }

            Template::fromAssertionBag($assertions)->executeOnFluent($fluent);

            $results[$fieldPath] = $fluent;
        }

        return $results;
    }

    /**
     * Set the alias of the container.
     *
     * @param string $alias
     *
     * @return $this
     */
    public function alias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Magic method for setting the alias of the container.
     *
     * @param string $methodName
     * @param array  $args
     *
     * @return $this
     *
     * @throws BadMethodCallException if the $methodName is invalid
     */
    public function __call($methodName, $args)
    {
        if ($methodName === 'as') {
            return call_user_func_array([$this, 'alias'], $args);
        }

        throw new BadMethodCallException(sprintf(
            'Unknown method name "%s"',
            $methodName
        ));
    }
}
