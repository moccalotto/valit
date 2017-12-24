<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit;

use Valit\Assertion\AssertionBag;

abstract class Value
{
    /**
     * Short hand for creating an AssertionBag.
     *
     * @return AssertionBag
     */
    public static function __callStatic($methodName, $args)
    {
        $template = new AssertionBag();

        return call_user_func_array(
            [$template, $methodName],
            $args
        );
    }
}
