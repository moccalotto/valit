<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Moccalotto\Valit\Providers;

use InvalidArgumentException;
use Moccalotto\Valit\Result;
use Moccalotto\Valit\Contracts\CheckProvider;
use Moccalotto\Valit\Traits\ProvideViaReflection;

class CustomCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check if $value passes a validation via a callback that returns a boolean.
     *
     * @Check(["passesCallback", "callback"])
     *
     * @param mixed $value
     * @param string $message The message of the result.
     * @param callable $callback The callback (that returns a bool).
     *
     * @return Result
     *
     * @throws InvalidArgumentException if $message is not a string or $callback is not callable.
     */
    public function checkPassesCallback($value, $message, $callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('$callback must be callable');
        }
        if (!is_string($message)) {
            throw new InvalidArgumentException('$message must be a string');
        }

        $success = (bool) $callback($value);

        return new Result($success, $message);
    }
}
