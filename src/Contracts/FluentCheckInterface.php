<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace Moccalotto\Valit\Contracts;

interface FluentCheckInterface
{
    /**
     * Have all checks been completed successfully?
     *
     * @return bool
     */
    public function success();

    /**
     * Alias of success.
     *
     * @return bool
     */
    public function valid();

    /**
     * Alias of hasErrors.
     *
     * @return bool
     */
    public function invalid();

    /**
     * Return true if there are errors.
     *
     * @return bool
     */
    public function hasErrors();

    /**
     * Throw exceptions if any failures has occurred or occur later in the execution stream.
     *
     * @return $this
     *
     * @throws ValidationException if any failures have occurred
     */
    public function orThrowException();
}