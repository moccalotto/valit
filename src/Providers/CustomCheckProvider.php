<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit\Providers;

use Valit\Util\Val;
use InvalidArgumentException;
use UnexpectedValueException;
use Valit\Util\CallbackChecker;
use Valit\Contracts\CheckProvider;
use Valit\Contracts\CustomChecker;
use Valit\Traits\ProvideViaReflection;
use Valit\Result\AssertionResult as Result;

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
        return $this->checkPassesChecker(
            $value,
            new CallbackChecker(
                Val::toString($message),
                Val::mustBe($callback, 'callable')
            )
        );
    }

    /**
     * Chec if $value passes a custom checker.
     *
     * @Check(["passesCustom", "passesChecker"])
     *
     * @param mixed                          $value
     * @param \Valit\Contracts\CustomChecker $checker
     *
     * @return Result
     *
     * @throws InvalidArgumentException if $checker is not an instance of CustomChecker
     * @throws UnexpectedValueException if $checker->check does not return an instance of Result
     */
    public function checkPassesChecker($value, $checker)
    {
        Val::mustBe($checker, CustomChecker::class, '$checker must be a Valit\Contracts\CustomChecker');

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
