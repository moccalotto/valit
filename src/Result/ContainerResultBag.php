<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit\Result;

use Valit\Contracts\Result;
use Valit\Exceptions\InvalidContainerException;

/**
 * Result of validating a container.
 */
class ContainerResultBag implements Result
{
    /**
     * @var AssertionResult[]
     */
    public $results;

    /**
     * @var string
     */
    public $varName;

    /**
     * Constructor.
     *
     * @param AssertionResult[] $results
     * @param string            $varName
     */
    public function __construct(array $results, $varName = 'Container')
    {
        $this->results = $results;
        $this->varName = $varName;
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
     * Add a result.
     *
     * @param string             $path           The path to the variable that passed/failed the tests
     * @param AssertionResultBag $valueValidator The validator that performed the assertions on the variable
     *
     * @return $this
     */
    public function addAssertionResultBag($path, AssertionResultBag $valueValidator)
    {
        foreach ($valueValidator->results() as $result) {
            $this->results[] = $result->withPath($path);
        }

        return $this;
    }

    /**
     * Did one or more tests fail?
     *
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors()) > 0;
    }

    /**
     * Did all tests pass?
     *
     * @return bool
     */
    public function success()
    {
        return !$this->hasErrors();
    }

    /**
     * Return all results.
     *
     * @param string|null $path If you want to filter by path, set this arg
     *
     * @return AssertionResult[]
     */
    public function results($path = null)
    {
        if ($path === null) {
            return $this->results;
        }

        return array_filter(
            $this->results,
            function ($result) use ($path) {
                return $result->path === $path;
            }
        );
    }

    /**
     * Return list of rendered errors.
     *
     * @param string|null $path Use this parameter if you only want to get the errors for a given path
     *
     * @return AssertionResult[]
     */
    public function errors($path = null)
    {
        return array_values(
            array_filter(
                $this->results($path),
                function ($result) {
                    return !$result->success();
                }
            )
        );
    }

    /**
     * Get all the error messages for a given path.
     *
     * @return string[] an array of rendered error messages
     */
    public function errorMessages()
    {
        return array_map(
            function ($result) {
                return $result->message;
            },
            $this->errors()
        );
    }

    /**
     * Get all the error messages for a given path.
     *
     * @param string|array $path
     *
     * @return string[] an array of rendered error messages
     */
    public function errorMessagesByPath($path)
    {
        $path = is_array($path) ? implode('/', $path) : $path;

        return array_map(
            function ($result) {
                return $result->message;
            },
            $this->errors($path)
        );
    }

    /**
     * Get all status messages.
     *
     * @return string[]
     */
    public function statusMessages()
    {
        return array_map(
            function ($result) {
                return sprintf(
                    '%s: %s',
                    $result->success() ? 'PASS' : 'FAIL',
                    $result->message()
                );
            },
            $this->results()
        );
    }

    /**
     * Get all the status messages for a given path.
     *
     * @param string|null $path Use this parameter if you only want to get the errors for a given path
     *
     * @return string[]
     */
    public function statusMessagesByPath($path)
    {
        return array_map(
            function ($result) {
                return sprintf(
                    '%s: %s',
                    $result->success() ? 'PASS' : 'FAIL',
                    $result->message()
                );
            },
            $this->results($path)
        );
    }

    /**
     * Throw an exception if this container has any errors.
     *
     * @return $this
     *
     * @throws InvalidContainerException if this container validation result contains any errors
     */
    public function orThrowException()
    {
        if ($this->hasErrors()) {
            throw new InvalidContainerException($this);
        }

        return $this;
    }
}
