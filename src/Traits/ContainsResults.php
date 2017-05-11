<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 *
 * @codingStandardsIgnoreFile
 */

namespace Moccalotto\Valit\Traits;

use Moccalotto\Valit\Result;
use Moccalotto\Valit\ValidationException;

trait ContainsResults
{
    /**
     * @var Result[]
     */
    protected $results = [];

    /**
     * @var string
     */
    protected $varName = 'value';

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var int
     */
    protected $successes = 0;

    /**
     * @var int
     */
    protected $failures = 0;

    /**
     * @var bool
     */
    protected $throwOnFailure;

    /**
     * Getter.
     *
     * @var string
     */
    public function varName()
    {
        return $this->varName;
    }

    /**
     * Getter.
     *
     * @var mixed
     */
    public function value()
    {
        return $this->value;
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
     * Add new result to the internal results list.
     *
     * @param Result $results
     *
     * @return $this
     *
     * @throws ValidationException if we are in throwOnFailure-mode and the result is an error
     */
    public function addCustomResult(Result $result)
    {
        $this->registerResult($result);

        return $this;
    }

    /**
     * Add new result to the internal results list.
     *
     * @param Result $results
     *
     * @throws ValidationException if we are in throwOnFailure-mode and the result is an error
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

    protected function registerManyResults(array $results)
    {
        foreach ($results as $result) {
            $this->registerResult($result);
        }
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
     * Return an array of rendered error messages.
     *
     * @return string[]
     */
    public function errorMessages()
    {
        $messages = [];

        foreach ($this->renderedResults() as $message => $success) {
            if (!$success) {
                $messages[] = $message;
            }
        }

        return $messages;
    }
}
