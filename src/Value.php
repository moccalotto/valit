<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit;

use Valit\Assertion\AssertionBag;

/**
 * Factory for AssertionBags.
 */
class Value
{
    /**
     * Short hand for creating an AssertionBag.
     *
     * @return AssertionBag
     */
    public static function __callStatic($methodName, $args)
    {
        return call_user_func_array(
            [new AssertionBag(), $methodName],
            $args
        );
    }

    /**
     * Short hand for creating an AssertionBag.
     *
     * @return AssertionBag
     */
    public function __call($methodName, $args)
    {
        return call_user_func_array(
            [new AssertionBag(), $methodName],
            $args
        );
    }
}
