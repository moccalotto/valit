<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Traits;

use Valit\Result\AssertionResult;

trait ContainsResults
{
    /**
     * @var AssertionResult[]
     *
     * @internal
     */
    public $results = [];

    /**
     * @var string
     *
     * @internal
     */
    public $varName = 'value';

    /**
     * @var mixed
     *
     * @internal
     */
    public $value;

    /**
     * @var int
     *
     * @internal
     */
    public $successes = 0;

    /**
     * @var int
     *
     * @internal
     */
    public $failures = 0;

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
     * Set the variable name alias.
     *
     * @param string $varName
     *
     * @return $this
     */
    public function alias($varName)
    {
        $this->varName = $varName;

        return $this;
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
     * Have all checks been completed successfully?
     *
     * @return bool
     */
    public function success()
    {
        return $this->failures === 0;
    }

    /**
     * Alias of success.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->success();
    }

    /**
     * Alias of hasErrors.
     *
     * @return bool
     */
    public function invalid()
    {
        return $this->hasErrors();
    }

    /**
     * Return true if there are errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return $this->failures > 0;
    }

    /**
     * Get the validated value, but use a fallback if the validation failed.
     *
     * @param mixed $valueIfValidationFails
     *
     * @return mixed
     */
    public function valueOr($valueIfValidationFails)
    {
        if ($this->failures === 0) {
            return $this->value;
        }

        return $valueIfValidationFails;
    }

    /**
     * Get the assertion results.
     *
     * @return AssertionResult[]
     */
    public function results()
    {
        return $this->results;
    }

    /**
     * Add new result to the internal results list.
     *
     * @param AssertionResult $results
     *
     * @return $this
     *
     * @throws InvalidValueException if we are in throwOnFailure-mode and the result is an error
     */
    public function addAssertionResult(AssertionResult $result)
    {
        $this->results[] = $result;

        if ($result->success()) {
            ++$this->successes;
        } else {
            ++$this->failures;
        }
        // early return
        return $this;
    }

    /**
     * Render the results as [message => success-status] map.
     *
     * The result is formarted as [message1 => success1, message2 => success2, ...]
     *
     * @return array
     */
    public function renderedResults()
    {
        $output = [];

        foreach ($this->results as $result) {
            $key = $result->renderMessage($this->varName, $this->value);

            $output[$key] = $result->success();
        }

        return $output;
    }

    /**
     * Get all failed assertion results.
     *
     * @return AssertionResult[]
     */
    public function errors()
    {
        return array_filter($this->results, function ($result) {
            return !$result->success();
        });
    }

    /**
     * Return an array of rendered error messages.
     *
     * @return string[]
     */
    public function errorMessages()
    {
        return array_values(
            array_map(
                function ($error) {
                    return $error->renderMessage($this->varName, $this->value);
                },
                $this->errors()
            )
        );
    }
}
