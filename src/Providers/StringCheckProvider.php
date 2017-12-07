<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use InvalidArgumentException;
use Valit\Result;
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;

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
        if (!$this->checkStringable($pattern)->success()) {
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
        $success = is_scalar($value)
            || is_object($value) && method_exists($value, '__toString');

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
        if (!$this->checkStringable($startsWith)->success()) {
            throw new InvalidArgumentException('Second argument cannot be cast to a string');
        }

        $success = is_scalar($value)
            && ($startsWith === '' || strpos($value, $startsWith) === 0);

        return new Result($success, '{name} must start with the string "{0}"', [$startsWith]);
    }

    /**
     * Check if $value ends with a given string.
     *
     * @Check("endsWith")
     *
     * @param mixed  $value
     * @param string $startsWith
     *
     * @return Result
     */
    public function checkEndsWith($value, $endsWith)
    {
        if (!$this->checkStringable($endsWith)->success()) {
            throw new InvalidArgumentException('Second argument cannot be cast to a string');
        }

        $success = is_scalar($value)
            && ($endsWith === '' || substr($value, -strlen($endsWith)) === $endsWith);

        return new Result($success, '{name} must end with the string {0}', [$endsWith]);
    }

    /**
     * Check if $value contains a given string.
     *
     * @Check(["containsString", "containsTheString"])
     *
     * @param mixed  $value
     * @param string $startsWith
     *
     * @return Result
     */
    public function checkContainsString($value, $contains)
    {
        if (!$this->checkStringable($contains)->success()) {
            throw new InvalidArgumentException('Second argument cannot be cast to a string');
        }

        $success = is_scalar($value)
            && ($contains === '' || strpos($value, $contains) !== false);

        return new Result($success, '{name} must contain the string "{0}"', [$contains]);
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
        if (!is_int($length)) {
            throw new InvalidArgumentException('Second argument must be an integer');
        }

        $success = is_scalar($value) && mb_strlen($value) < $length;

        return new Result($success, '{name} must be a string that is shorter than {0}', [$length]);
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
        if (!is_int($length)) {
            throw new InvalidArgumentException('Second argument must be an integer');
        }

        $success = is_scalar($value) && mb_strlen($value) > $length;

        return new Result($success, '{name} must be a string that is longer than {0}', [$length]);
    }

    /**
     * Check if $value is a string that has the length $length.
     *
     * @Check(["hasLength", "length"])
     *
     * @param mixed $value
     * @param int   $length
     *
     * @return Result
     */
    public function checkLength($value, $length)
    {
        if (!is_int($length)) {
            throw new InvalidArgumentException('Second argument must be an integer');
        }

        $success = is_scalar($value) && mb_strlen($value) === $length;

        return new Result($success, '{name} must be a string that has the length {0}', [$length]);
    }
}
