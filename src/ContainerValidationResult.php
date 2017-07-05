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

/**
 * Result of validating a container.
 */
class ContainerValidationResult
{
    /**
     * @var Fluent[]
     */
    protected $results;

    /**
     * Constructor.
     *
     * @param Fluent[] $results
     */
    public function __construct(array $results)
    {
        $this->results = $results;
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
     * @return array associative array of [ path => [results] ]
     */
    public function results()
    {
        return array_map(function ($fluent) {
            return $fluent->results();
        }, $this->results);
    }

    /**
     * Return list of rendered errors.
     *
     * @return array associative array of [ path => [errors] ]
     */
    public function errors()
    {
        return array_filter(array_map(function ($fluent) {
            return $fluent->errorMessages();
        }, $this->results));
    }

    /**
     * Get all results as rendered strings.
     *
     * @return array associative array of [ path => [message => success] ]
     */
    public function renderedResults()
    {
        return array_map(function ($fluent) {
            return $fluent->renderedResults();
        }, $this->results);
    }

    /**
     * Get all the error messages for a given path.
     *
     * @param string|array $path
     *
     * @return array an array of rendered error messages
     */
    public function errorMessagesByPath($path)
    {
        $key = is_array($path) ? implode('/', $path) : $path;

        return isset($this->results[$key])
            ? $this->results[$key]->errorMessages()
            : [];
    }
}
