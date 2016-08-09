<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace Moccalotto\Valit;

use BadMethodCallException;
use Moccalotto\Valit\Contracts\CheckManager;

class Fluent
{
    /**
     * @var CheckManager
     */
    protected $manager;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $varName = 'value';

    /**
     * @var bool
     */
    protected $throwOnFailure;

    /**
     * @var Result[]
     */
    protected $results = [];

    /**
     * @var int
     */
    protected $successes = 0;

    /**
     * @var int
     */
    protected $failures = 0;

    /**
     * Add new result to the internal results list.
     *
     * @param Result $results
     *
     * @throws ValidationException if we are in throwOnFailure-mode and the result is an error.
     */
    protected function registerResult(Result $result)
    {
        $this->results[] = $result;

        if ($result->success()) {
            ++$this->successes;

            // early return
            return;
        }

        ++$this->failures;

        if ($this->throwOnFailure) {
            throw new ValidationException(
                $result->renderErrorMessage($this->varName, $this->value),
                $this->varName,
                $this->value,
                $this->results
            );
        }
    }

    /**
     * Constructor.
     *
     * @param CheckManager $manager         The manager that contains all our checks
     * @param mixed        $value           The value we are validating
     * @param bool         $throwOnFailure  Should we throw an exception as soon as we encounter a failed result
     */
    final public function __construct(CheckManager $manager, $value, $throwOnFailure)
    {
        $this->manager = $manager;
        $this->value = $value;
        $this->throwOnFailure = (bool) $throwOnFailure;
    }

    public function alias($varName)
    {
        $this->varName = $varName;

        return $this;
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
        if ($this->manager->hasCheck($methodName)) {
            $result = $this->manager->executeCheck($methodName, $this->value, $args);

            $this->registerResult($result);

            return $this;
        }

        if ($methodName === 'as') {
            return $this->alias($args[0]);
        }

        throw new BadMethodCallException(sprintf(
            'Unknown method name "%s"',
            $methodName
        ));
    }

    /**
     * Have all checks been completed successfully?
     *
     * @return bool
     */
    public function success()
    {
        return $this->failures === 0;
    }

    /**
     * Get the results.
     *
     * @return Result[]
     */
    public function results()
    {
        return $this->results;
    }

    /**
     * Get the results as an associative array.
     *
     * The result is formarted as [message1 => success1, message2 => success2, ...]
     *
     * @return array
     */
    public function renderedResults()
    {
        $output = [];

        foreach ($this->results as $result) {
            $key = $result->renderErrorMessage($this->varName, $this->value);

            $output[$key] = $result->success();
        }

        return $output;
    }

    /**
     * Throw exceptions if any failures has occurred or occur later in the execution stream.
     *
     * @return $this
     *
     * @throws ValidationException if any failures have occurred
     */
    public function orThrowException()
    {
        if ($this->failures) {
            throw new ValidationException(
                sprintf('Failed %d out of %d validation checks', $this->failures, count($this->results)),
                $this->varName,
                $this->value,
                $this->results
            );
        }

        return $this;
    }
}
