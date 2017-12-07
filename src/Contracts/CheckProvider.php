<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Contracts;

interface CheckProvider
{
    /**
     * Return an array of the checks provided by this provider.
     *
     * @return array
     *
     * The returned array has the format
     * [
     *      $checkName => $callback,
     * ]
     */
    public function provides();
}
