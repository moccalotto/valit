<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Traits;

use Valit\Result\AssertionResult;

trait CanString
{
    public function canString($value)
    {
        return is_scalar($value)
            || is_object($value) && method_exists($value, '__toString');
    }
}
