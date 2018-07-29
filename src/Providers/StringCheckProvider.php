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
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;
use Valit\Result\AssertionResult as Result;

class StringCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check if $value contains only hexidecimal characters.
     *
     * @Check(["isHexString", "hexString", "isHex"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkHexString($value)
    {
        $success = is_string($value) && ctype_xdigit((string) $value);

        return new Result($success, '{name} must contain only hexidecimal characters');
    }

    /**
     * Check if $value contains only decimal characters.
     *
     * @Check(["decimalString", "isDecimalString"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkDecimalString($value)
    {
        $success = is_string($value) && ctype_digit((string) $value);

        return new Result($success, '{name} must contain only decimal characters');
    }

    /**
     * Check if $value is a valid alphabetical currency code string.
     *
     * @Check(["currencyCode", "isCurrencyCode", "isAlphaCurrencyCode", "alphaCurrencyCode"])
     *
     * @see {https://en.wikipedia.org/wiki/ISO_4217}
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkCurrencyCode($value)
    {
        $success = ctype_upper($value) && strlen($value) === 3;

        return new Result($success, '{name} must be an upper case, three letter currency code');
    }

    /**
     * Check if $value.
     *
     * @Check(["numericCurrencyCode", "currencyNumber", "isNumericCurrencyCode", "isCurrencyNumber"])
     *
     * @see {https://en.wikipedia.org/wiki/ISO_4217#Currency_numbers}
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkCurrencyNumber($value)
    {
        $success = ctype_digit($value) && strlen($value) === 3;

        return new Result($success, '{name} must be an upper case, three letter currency code');
    }

    /**
     * Check if $value contains a syntax-valid email address.
     *
     * @Check(["isEmail", "email", "isEmailAddress", "emailAddress"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkEmail($value)
    {
        $success = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;

        return new Result($success, '{name} must be a syntax-valid email address');
    }

    /**
     * Check if $value is only uppercase characters.
     *
     * @Check(["isUppercase", "uppercase"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkUppercase($value)
    {
        $success = ctype_upper($value);

        return new Result($success, '{name} must only contain upper case latin letters');
    }

    /**
     * Check if $value is only lowercase characters.
     *
     * @Check(["isLowercase", "lowercase"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkLowercase($value)
    {
        $success = ctype_lower($value);

        return new Result($success, '{name} must only contain lower case latin letters');
    }

    /**
     * Check if $value contains only alpha-numeric characters.
     *
     * @Check(["isAlphaNumeric", "alphaNumeric", "alphaNum", "isAlphaNum"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkAlphaNumeric($value)
    {
        $success = ctype_alnum($value);

        return new Result($success, '{name} must only contain alpha-numeric characters');
    }

    /**
     * Check if $value matches a given regular regex.
     *
     * @Check(["matches", "matchesRegex"])
     *
     * @param mixed  $value
     * @param string $pattern
     *
     * @return Result
     */
    public function checkMatchesRegex($value, $pattern)
    {
        if (!Val::stringable($pattern)) {
            throw new InvalidArgumentException('Second argument cannot be cast to a string');
        }

        $success = is_string($value) && @preg_match($pattern, $value);

        if (preg_last_error() !== PREG_NO_ERROR) {
            throw new InvalidArgumentException('Second argument is not a valid regular expression', preg_last_error());
        }

        return new Result($success, '{name} must match the regular expression: {0}', [$pattern]);
    }

    /**
     * Check if a given value can be converted to a string in a meaningful way.
     *
     * @Check(["stringable", "isStringable", "stringCastable", "isStringCastable"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkStringable($value)
    {
        $success = Val::stringable($value);

        return new Result($success, '{name} must be a string or string-castable');
    }

    /**
     * Check if $value starts with a given string.
     *
     * @Check(["startsWith", "beginsWith"])
     *
     * @param mixed  $value
     * @param string $startsWith
     *
     * @return Result
     */
    public function checkStartsWith($value, $startsWith)
    {
        $startsWith = Val::toString($startsWith, 'Second argument cannot be cast to a string');

        $success = is_scalar($value)
            && ($startsWith === '' || strpos((string) $value, $startsWith) === 0);

        return new Result($success, '{name} must start with the string "{0}"', [$startsWith]);
    }

    /**
     * Check if $value ends with a given string.
     *
     * @Check("endsWith")
     *
     * @param mixed  $value
     * @param string $endsWith
     *
     * @return Result
     */
    public function checkEndsWith($value, $endsWith)
    {
        $endsWith = Val::toString($endsWith, 'Second argument cannot be cast to a string');

        $success = is_scalar($value)
            && ($endsWith === '' || substr((string) $value, -strlen($endsWith)) === $endsWith);

        return new Result($success, '{name} must end with the string {0}', [$endsWith]);
    }

    /**
     * Check if $value contains a given substring.
     *
     * @Check(["containsString", "containsTheString"])
     *
     * @param mixed  $value
     * @param string $substring
     *
     * @return Result
     */
    public function checkContainsString($value, $substring)
    {
        $substring = Val::toString($substring, 'Second argument cannot be cast to a string');

        $success = is_scalar($value)
            && ($substring === '' || strpos((string) $value, $substring) !== false);

        return new Result($success, '{name} must contain the string "{0}"', [$substring]);
    }

    /**
     * Check if $value is a string that is shorter than $length.
     *
     * @Check(["shorterThan", "stringShorterThan", "isShorterThan"])
     *
     * @param mixed $value
     * @param int   $length
     *
     * @return Result
     */
    public function checkShorterThan($value, $length)
    {
        $length = Val::toInt($length, 'Second argument must be an integer');

        return $this->checkRelativeLength($value, '<', $length);
    }

    /**
     * Check if $value is a string that is longer than $length.
     *
     * @Check(["longerThan", "stringLongerThan", "isLongerThan", "isStringLongerThan"])
     *
     * @param mixed $value
     * @param int   $length
     *
     * @return Result
     */
    public function checkLongerThan($value, $length)
    {
        $length = Val::toInt($length, 'Second argument must be an integer');

        return $this->checkRelativeLength($value, '>', $length);
    }

    /**
     * Check if $value is a string where the length compares to $against using the $operator.
     *
     * Examples:
     *
     * ```php
     * // length > 20
     * Check::that($foo)->lengthIs('>', 20);
     *
     * // length <= 255
     * Check::that($foo)->lengthIs('<=', 255);
     *
     * // alternative:
     * Check::that($foo)->lengthIs('≤', 255);
     *
     * // check length = 40
     * Check::that($foo)->lengthIs('=', 40);
     * Check::that($foo)->lengthIs(40);     // alt. syntax
     * ```
     *
     * @Check(["stringWhereLength", "isStringWhereLength", "whereLength", "length", "hasLength", "withLength"])
     *
     * @param mixed      $value    The inspected variable
     * @param string|int $operator Must be one of >, <, =, >=, <=, ≥, ≤
     * @param int        $against  The length we should compare to.
     *
     * @return Result
     */
    public function checkRelativeLength($value, $operator, $against = null)
    {
        Val::mustBe($against, ['intable', 'null'], 'Third argument must be an integer');

        if (is_int($operator) && is_null($against)) {
            $against = $operator;
            $operator = '=';
        }

        Val::mustBe($operator, 'string', 'Second argument must be a string');

        $length = Val::stringable($value) ? mb_strlen($value) : NAN;
        $message = '{name} must be a string where length {0:raw} {1:int}';

        if ($operator === '>') {
            return new Result($length > $against, $message, ['>', $against]);
        }

        if ($operator === '>=' || $operator === '≥') {
            return new Result($length >= $against, $message, ['≥', $against]);
        }

        if ($operator === '=') {
            return new Result($length === $against, $message, ['is', $against]);
        }

        if ($operator === '<') {
            return new Result($length < $against, $message, ['<', $against]);
        }

        if ($operator === '<=' || $operator === '≤') {
            return new Result($length < $against, $message, ['≤', $against]);
        }

        throw new InvalidArgumentException('Second arhument must be one of [>, <, =, >=, ≥, <=, ≤]');
    }

    /**
     * Check if $value is a string where the length is between $min and $max.
     *
     * @Check(["lengthBetween", "lengthInRange", "lengthMinMax"])
     *
     * @param mixed $value The value
     * @param int   $min   The minimal allowed length.
     * @param int   $max   The maximum allowed length.
     *
     * @return Result
     */
    public function checkLengthInRange($value, $min, $max)
    {
        Val::mustBe($min, 'int', '$min must be an integer');
        Val::mustBe($max, 'int', '$max must be an integer');

        if ($max < $min) {
            throw new InvalidArgumentException('$max must be ≥ $min');
        }

        $success = Val::is($value, 'stringable')
            && mb_strlen($value) >= $min
            && mb_strlen($value) <= $max;

        return new Result(
            $success,
            '{name} must be a string with a length in the range [{0:int}..{1:int}]',
            [$min, $max]
        );
    }
}
