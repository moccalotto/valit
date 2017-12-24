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
 * Utility class for converting variables
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
     * @throws InvalidArgumentException if $value could not be "nicely" converted to string
     *
     * @param mixed       $value
     * @param string|null $errorMessage
     *
     * @return string
     */
    public static function toString($value, $errorMessage = null)
    {
        if (!static::canString($value)) {
            if ($errorMessage === null) {
                $errorMessage = sprintf(
                    'The given %s could not be converted to string',
                    gettype($value)
                );
            }
            throw new InvalidArgumentException($errorMessage);
        }

        return (string) $value;
    }

    /**
     * Covnert a string (or stringable value) to an integer.
     *
     * @throws InvalidArgumentException if $value could not be nicely converted to int
     *
     * @param mixed       $value
     * @param string|null $errorMessage
     *
     * @return int
     */
    public static function toInt($value, $errorMessage = null)
    {
        if ($errorMessage === null) {
            $errorMessage = sprintf(
                'The given %s could not be converted to integer',
                gettype($value)
            );
        }

        $strval = static::toString($value, $errorMessage);

        if (!is_numeric($strval)) {
            throw new InvalidArgumentException($errorMessage);
        }

        if (intval($strval) != floatval($strval)) {
            throw new InvalidArgumentException($errorMessage);
        }

        return (int) $strval;
    }
}
