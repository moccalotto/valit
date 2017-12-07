<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit;

use Valit\Exceptions\InvalidContainerException;

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
     * @var string
     */
    protected $alias;

    /**
     * Constructor.
     *
     * @param Fluent[] $results
     */
    public function __construct(array $results, $alias = 'Container')
    {
        $this->results = $results;
        $this->alias = $alias;
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

    public function alias()
    {
        return $this->alias;
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
