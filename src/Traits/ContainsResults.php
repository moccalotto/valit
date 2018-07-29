<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit\Traits;

use Valit\Result\AssertionResult;

trait ContainsResults
{
    /**
     * Internal.
     *
     * @var AssertionResult[]
     */
    public $results = [];

    /**
     * Internal.
     *
     * @var string
     */
    public $varName = 'value';

    /**
     * Internal.
     *
     * @var mixed
     */
    public $value;

    /**
     * Internal.
     *
     * @var int
     */
    public $successes = 0;

    /**
     * Internal.
     *
     * @var int
     */
    public $failures = 0;

    /**
     * The the variable name alias.
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
     * Get the validated value.
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
        return $this->hasErrors()
            ? $valueIfValidationFails
            : $this->value;
    }

    /**
     * Get the assertie results.
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
     * @param AssertionResult $result
     *
     * @return $this
     *
     * @throws \InvalidValueException if we are in throwOnFailure-mode and the result is an error
     */
    public function addAssertionResult(AssertionResult $result)
    {
        $this->results[] = $result->normalize($this->varName, $this->value);

        if ($result->success()) {
            ++$this->successes;
        } else {
            ++$this->failures;
        }

        return $this;
    }

    /**
     * Get all failed assertion results.
     *
     * @return AssertionResult[]
     */
    public function errors()
    {
        return array_values(
            array_filter($this->results, function ($result) {
                return !$result->success();
            })
        );
    }

    /**
     * Return an array of rendered error messages.
     *
     * @return string[]
     */
    public function errorMessages()
    {
        return array_map(
            function ($result) {
                return $result->message();
            },
            $this->errors()
        );
    }

    /**
     * Status messages.
     *
     * @return string[]
     */
    public function statusMessages()
    {
        return array_map(
            function ($result) {
                $status = $result->success()
                    ? 'PASS: '
                    : 'FAIL: ';

                return $status.$result->message();
            },
            $this->results()
        );
    }

    /**
     * Get the first error message.
     *
     * @return string|null
     */
    public function firstErrorMessage()
    {
        foreach ($this->results as $result) {
            if (!$result->success()) {
                return $result->message;
            }
        }

        return null;
    }
}
