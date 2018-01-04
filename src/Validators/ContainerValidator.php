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
use Valit\Util\Val;
use LogicException;
use BadMethodCallException;
use Valit\Util\FlatContainer;
use Valit\Assertion\AssertionBag;
use Valit\Result\AssertionResult;
use Valit\Result\AssertionResultBag;
use Valit\Result\ContainerResultBag;
use Valit\Assertion\AssertionBagFactory;

/**
 * Validate a container (variable with array access).
 *
 * @method $this as(string $name) set the alias of the container
 */
class ContainerValidator extends ContainerResultBag
{
    /**
     * Internal.
     *
     * @var Manager
     */
    public $manager;

    /**
     * Internal.
     *
     * @var array|object
     */
    public $container;

    /**
     * Internal.
     *
     * @var FlatContainer
     */
    public $flatContainer;

    /**
     * Internal.
     *
     * @var bool
     */
    public $throwOnFailure;

    /**
     * Constructor.
     *
     * @param Manager           $manager
     * @param array|Traversable $container
     * @param bool              $throwOnFailure
     */
    public function __construct(Manager $manager, $container, $throwOnFailure)
    {
        $this->manager = $manager;
        $this->container = $container;
        $this->throwOnFailure = (bool) $throwOnFailure;
        $this->flatContainer = new FlatContainer($container);
        parent::__construct([], 'Container');
    }

    /**
     * Check container against a number of assertions.
     *
     * @param array|Traversable $containerAssertionMap
     *
     * @return ContainerResultBag
     */
    public function passes($containerAssertionMap)
    {
        if (!Val::iterable($containerAssertionMap)) {
            throw new LogicException('$containerAssertionMap must be iterable');
        }

        foreach ($containerAssertionMap as $fieldNameGlob => $assertions) {
            $normalizedAssertions = AssertionBagFactory::create($assertions);
            $this->executeAndAdd($fieldNameGlob, $normalizedAssertions);
        }

        return $this->throwOnFailure
            ? $this->orThrowException()
            : $this;
    }

    /**
     * Execute an array of assertions on a number of values.
     *
     * @param string       $fieldNameGlob A field name glob (such as "address" or "order.*.id")
     * @param AssertionBag $assertions    A normalized array of assertions
     */
    protected function executeAndAdd($fieldNameGlob, $assertions)
    {
        $fieldsToValidate = $this->flatContainer->find($fieldNameGlob);

        $optional = $assertions->hasFlag('optional');

        if ($fieldsToValidate === []) {
            $message = $optional ? '{name} is optional' : '{name} must be present';
            $fieldResults = new AssertionResultBag($this->container, $fieldNameGlob);
            $fieldResults->addAssertionResult(new AssertionResult($optional, $message));
            $this->addAssertionResultBag($fieldNameGlob, $fieldResults);
        }

        foreach ($fieldsToValidate as $fieldPath => $value) {
            $fieldResults = new ValueValidator($this->manager, $value, $this->throwOnFailure);
            $fieldResults->alias($fieldPath);

            if (!$optional) {
                $fieldResults->addAssertionResult(new AssertionResult(true, '{name} must be present'));
            }

            $assertions->applyToValidator($fieldResults);

            $this->addAssertionResultBag($fieldPath, $fieldResults);
        }
    }

    /**
     * Magic method for setting the alias of the container.
     *
     * @param string $methodName
     * @param array  $args
     *
     * @return mixed
     *
     * @throws BadMethodCallException if the $methodName is invalid
     */
    public function __call($methodName, $args)
    {
        if ($methodName === 'as') {
            $this->alias($args[0]);

            return $this;
        }

        // Forward call to ValueValidator
        $valueValidator = new ValueValidator($this->manager, $this->container, $this->throwOnFailure);

        return $valueValidator->__call($methodName, $args);
    }
}
