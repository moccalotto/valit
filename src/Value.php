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
     * @return \Valit\Assertion\Template
     */
    public static function __callStatic($methodName, $args)
    {
        $template = new Assertion\Template();

        return call_user_func_array(
            [$template, $methodName],
            $args
        );
    }
}
