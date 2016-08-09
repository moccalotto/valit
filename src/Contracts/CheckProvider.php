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
