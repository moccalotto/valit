<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Util;

use InvalidArgumentException;

/**
 * Utility class for converting variables.
 */
abstract class Val
{
    /**
     * Can the given value be coerced into a string.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function canString($value)
    {
        return is_string($value)
            || is_int($value)
            || is_float($value)
            || is_object($value) && method_exists($value, '__toString');
    }

    /**
     * Coerce a value to string.
     *
     * Throw an exception if not possible.
     *
     * Strings, integers, floats and objects with a __toString method can be coerced.
     *
     * @param mixed       $value
     * @param string|null $error
     *
     * @return string
     *
     * @throws InvalidArgumentException if $value could not be "nicely" converted to string
     */
    public static function toString($value, $error = null)
    {
        if (!static::canString($value)) {
            throw new InvalidArgumentException($error ?: sprintf(
                'The given %s could not be converted to string',
                gettype($value)
            ));
        }

        return (string) $value;
    }

    /**
     * Convert a variable to an integer.
     *
     * @param mixed  $value The value to be converted.
     *                      Intgegers are returned as-is.
     *                      Floats without fractions are converted if: PHP_INT_MIN ≤ $value ≤ PHP_INT_MAX
     *                      Strings are coerced to floats if possible.
     *                      Objects with a __toString method will be treated as strings
     * @param string $error Error message to throw if the value could not be converted
     *
     * @return float
     */
    public static function toInt($value, $error = null)
    {
        $str = static::toString($value, $error);

        if (is_numeric($str) && intval($str) == floatval($str)) {
            return (int) $str;
        }

        throw new InvalidArgumentException($error ?: sprintf(
            'The given %s could not be converted to integer',
            gettype($value)
        ));
    }

    /**
     * Convert a variable to a float.
     *
     * @param mixed  $value The value to be converted.
     *                      Floats are returned as-is.
     *                      Integers are converted to floats if possible without losss of resolution.
     *                      Strings are coerced to floats if possible.
     *                      Objects with a __toString method will be treated as strings
     * @param string $error Error message to throw if the value could not be converted
     *
     * @return float
     */
    public static function toFloat($value, $error = null)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        $strval = static::toString($value, $error);

        if (!is_numeric($strval)) {
            throw new InvalidArgumentException($error ?: sprintf(
                'The given %s could not be converted to float',
                gettype($value)
            ));
        }

        return (float) $strval;
    }

    /**
     * Convert a variable to a bool.
     *
     * @param mixed  $value The value to be converted.
     *                      booleans will be returned as-is.
     *                      "true" will be converted to true.
     *                      "false" will be converted to false.
     *                      "1", "1.0", 1, 1.0 are converted to true.
     *                      "0", "0.0", 0, 0.0 are converted to false.
     *                      Objects with a __toString method be treated as strings
     * @param string $error Error message to throw if the value could not be converted
     *
     * @return bool
     */
    public function toBool($value, $error = null)
    {
        if (is_bool($value)) {
            return $value;
        }

        $str = static::toString($value, $error);

        switch ($str) {
            case '1':
            case '1.0':
            case 'true':
                return true;
            case '0':
            case '0.0':
            case 'false':
                return false;
        }

        throw new InvalidArgumentException($error ?: sprintf(
            'The given %s could not be converted to bool',
            gettype($value)
        ));
    }
}
