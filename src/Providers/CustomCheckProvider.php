<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use Valit\Result;
use InvalidArgumentException;
use UnexpectedValueException;
use Valit\CustomChecker;
use Valit\CustomCallbackChecker;
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;

class CustomCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check if $value passes a validation via a callback that returns a boolean.
     *
     * @Check("passesCallback")
     *
     * @param mixed    $value
     * @param string   $message  the message of the result
     * @param callable $callback the callback (that returns a bool)
     *
     * @return Result
     *
     * @throws InvalidArgumentException if $message is not a string or $callback is not callable
     */
    public function checkPassesCallback($value, $message, $callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('$callback must be callable');
        }
        if (!is_string($message)) {
            throw new InvalidArgumentException('$message must be a string');
        }

        return $this->checkPassesChecker(
            $value,
            new CustomCallbackChecker($message, $callback)
        );
    }

    /**
     * Chec if $value passes a custom checker.
     *
     * @Check(["passesCustom", "passesChecker"])
     *
     * @param mixed         $value
     * @param CustomChecker $checker
     *
     * @return Result
     *
     * @throws InvalidArgumentException if $checker is not an instance of CustomChecker
     * @throws UnexpectedValueException if $checker->check does not return an instance of Result
     */
    public function checkPassesChecker($value, $checker)
    {
        if (!is_a($checker, CustomChecker::class)) {
            throw new InvalidArgumentException(sprintf(
                '$checker must be an instance of %s',
                CustomChecker::class
            ));
        }

        $result = $checker->check($value);

        if (!is_a($result, Result::class)) {
            throw new UnexpectedValueException(sprintf(
                'Result of $checker->check() did not return an instnace of %s',
                Result::class
            ));
        }

        return $result;
    }
}
