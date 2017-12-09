<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Contracts;

interface CheckManager
{
    /**
     * Get or create the singleton instance.
     *
     * @return CheckManager
     */

    /**
     * Get the default (global) manager instance.
     *
     * If no instance is configured, a new one is created via the create() factory method.
     *
     * @return CheckManager
     *
     * @see create
     */
    public static function instance();

    /**
     * Factory method.
     *
     * Create a Manager with all the default providers loaded
     * as well as the providers defined in $additionalCheckProviders
     *
     * @param string[] $additionalCheckProviders
     *
     * @return CheckManager
     */
    public static function create($additionalCheckProviders = []);

    /**
     * Check if the manager can perform a given check.
     *
     * @param string $checkName
     *
     * @return bool
     */
    public function hasCheck($checkName);

    /**
     * Execute a check.
     *
     * @param string $checkName
     * @param mixed  $value
     * @param array  $args
     *
     * @return \Valit\Result\AssertionResult
     */
    public function executeCheck($checkName, $value, array $args);
}
