<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit;

abstract class Value
{
    /**
     * Short hand to creating templates.
     *
     * @return Template
     */
    public static function __callStatic($methodName, $args)
    {
        return call_user_func_array(
            [Check::value(), $methodName],
            $args
        );
    }
}
