<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Validators;

use Traversable;
use Valit\Manager;
use LogicException;
use Valit\Template;
use BadMethodCallException;
use Valit\Util\FlatContainer;
use Valit\Result\AssertionResultBag;
use Valit\Result\AssertionResult;
use Valit\Result\ContainerResultBag;
use Valit\Assertion\AssertionNormalizer;

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
     * @var FlatContainer
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
     * @var ContainerResultBag
     */
    public $results;

    /**
     * Constructor.
     *
     * @param Manager      $manager
     * @param array|object $container
     * @param bool         $throwOnFailure
     */
    public function __construct(Manager $manager, $container, $throwOnFailure)
    {
        $this->manager = $manager;
        $this->throwOnFailure = $throwOnFailure;
        $this->container = $container;
        $this->flatContainer = new FlatContainer($container);
        $this->results = new ContainerResultBag([]);
    }

    /**
     * Check container against a number of assertions.
     *
     * @param Traversable|array $containerAssertionMap
     *
     * @return ContainerResultBag
     */
    public function passes($containerAssertionMap)
    {
        if (!$this->isTraversable($containerAssertionMap)) {
            throw new LogicException('$validation must be an array or a Traversable object');
        }

        foreach ($containerAssertionMap as $fieldNameGlob => $assertions) {
            $normalizedAssertions = AssertionNormalizer::normalize($assertions);
            $this->executeAndAdd($fieldNameGlob, $normalizedAssertions);
        }

        return $this->results;
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
    protected function executeAndAdd($fieldNameGlob, $assertions)
    {
        $fieldsToValidate = $this->flatContainer->find($fieldNameGlob);

        if ($fieldsToValidate === []) {
            $message = $assertions->isOptional() ? '{name} is optional' : '{name} must be present';
            $assertionResultBag = new AssertionResultBag($this->container, $fieldNameGlob, $this->throwOnFailure);
            $assertionResultBag->addAssertionResult(new AssertionResult($assertions->isOptional(), $message));

            $this->results->add($fieldNameGlob, $assertionResultBag);

            return;
        }

        foreach ($fieldsToValidate as $fieldPath => $value) {
            $valueValidator = new ValueValidator($this->manager, $value, $this->throwOnFailure);
            $valueValidator->alias($fieldPath);

            if (!$assertions->isOptional()) {
                $valueValidator->addAssertionResult(new AssertionResult(true, '{name} must be present'));
            }

            Template::fromAssertionBag($assertions)->applyToValidator($valueValidator);

            $this->results->add($fieldPath, $valueValidator);
        }
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
        $this->results->alias($alias);

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
