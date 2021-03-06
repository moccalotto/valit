<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit\Validators;

use Exception;
use Valit\Manager;
use Valit\Util\Val;
use BadMethodCallException;
use Valit\Result\AssertionResultBag;
use Valit\Exceptions\InvalidValueException;

/**
 * Validate a value.
 *
 * @method $this as(string $name) set the alias of the value to render pretty status messages
 */
class ValueValidator extends AssertionResultBag
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
     * @var bool
     */
    public $throwOnFailure;

    /**
     * Constructor.
     *
     * @param Manager $manager        The manager that contains all our checks
     * @param mixed   $value          The value we are validating
     * @param bool    $throwOnFailure Should we throw an exception as soon as we encounter a failed result
     */
    public function __construct(Manager $manager, $value, $throwOnFailure = false)
    {
        $this->manager = $manager;
        $this->throwOnFailure = (bool) $throwOnFailure;
        parent::__construct($value, 'value');
    }

    /**
     * Execute checks by "calling" them.
     *
     * @param string $methodName
     * @param array  $args
     *
     * @return $this
     */
    public function __call($methodName, $args)
    {
        if ($methodName === 'as') {
            $this->alias($args[0]);

            return $this;
        }

        if (!$this->manager->hasCheck($methodName)) {
            throw new BadMethodCallException(sprintf(
                'Unknown method name "%s"',
                $methodName
            ));
        }

        $this->executeCheck($methodName, $args);

        return $this;
    }

    /**
     * Execute a check.
     *
     * @param string $checkName
     * @param array  $args
     *
     * @return $this
     */
    public function executeCheck($checkName, array $args)
    {
        $result = $this->manager->executeCheck($checkName, $this->value, $args);

        $this->addAssertionResult($result);

        if ($this->throwOnFailure) {
            return $this->orThrowException();
        }

        return $this;
    }

    /**
     * Throw exceptions if any failures has occurred or occur later in the execution stream.
     *
     * @param \Exception|\Throwable $error
     *
     * @return $this
     *
     * @throws InvalidValueException if any failures have occurred
     */
    public function orThrowException($error = null)
    {
        // No errors. Do nothing.
        if ($this->failures === 0) {
            return $this;
        }

        if ($error === null) {
            throw new InvalidValueException(
                $this->varName,
                $this->value,
                $this->results
            );
        }

        throw Val::mustBe($error, 'throwable');

        return $this;
    }

    /**
     * Throw exceptions if any failures has occurred or occur later in the execution stream.
     *
     * Alias of orThrowException()
     *
     * @see self::orThrowException()
     *
     * @return $this
     *
     * @throws InvalidValueException if any failures have occurred
     */
    public function throwExceptionIfNotSuccessful()
    {
        return $this->orThrowException();
    }

    /**
     * Check container against a number of assertions.
     *
     * @param array|\Traversable $containerAssertionMap
     *
     * @return \Valit\Result\ContainerResultBag
     */
    public function contains($containerAssertionMap)
    {
        $containerValidator = new ContainerValidator($this->manager, $this->value, $this->throwOnFailure);

        return $containerValidator
            ->alias($this->varName ? $this->varName : 'container')
            ->passes($containerAssertionMap);
    }

    public function __debugInfo()
    {
        return [
            'value' => is_scalar($this->value) ? $this->value : Val::escape($this->value),
            'varName' => $this->varName,
            'failures' => $this->failures,
            'successes' => $this->successes,
            'throwOnFailure' => $this->throwOnFailure,
            'statusMessages' => $this->statusMessages(),
        ];
    }
}
