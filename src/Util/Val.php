<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Util;

use Closure;
use Countable;
use ArrayAccess;
use Traversable;
use LogicException;
use RuntimeException;
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
     * Can we traverse $value?
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function canTraverse($value)
    {
        return is_array($value)
            || is_a($value, Traversable::class);
    }

    /**
     * Can we access $value as an array?
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function hasArrayAccess($value)
    {
        return is_array($value)
            || is_a($value, ArrayAccess::class);
    }

    /**
     * Can we count the elements in $value?
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isCountable($value)
    {
        return is_array($value)
            || is_a($value, Countable::class);
    }

    /**
     * Coerce a value to string.
     *
     * Throw an exception if not possible.
     *
     * Strings, integers, floats and objects with a __toString method can be coerced.
     *
     * @param mixed       $value
     * @param string|null $error Error message to throw if the value could not be converted
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
     * @param mixed       $value The value to be converted.
     *                           Intgegers are returned as-is.
     *                           Floats without fractions are converted if: PHP_INT_MIN ≤ $value ≤ PHP_INT_MAX
     *                           Strings are coerced to floats if possible.
     *                           Objects with a __toString method will be treated as strings
     * @param string|null $error Error message to throw if the value could not be converted
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

    /**
     * Format a given value into a string.
     *
     * @param mixed  $value
     * @param string $format
     *
     * @return string
     *
     * @throws LogicException if $format is not known
     */
    public static function format($value, $format)
    {
        if ($format === 'normal') {
            return static::escape($value);
        }

        if ($format === 'raw') {
            return is_scalar($value) || is_callable([$value, '__toString'])
                ? (string) $value
                : static::escape($value);
        }

        if ($format === 'type') {
            return gettype($value);
        }

        if ($format === 'int') {
            return is_numeric($value)
                ? sprintf('%d', $value)
                : '[not numeric]';
        }

        if ($format === 'float') {
            return is_numeric($value)
                ? sprintf('%g', $value)
                : '[not numeric]';
        }

        if ($format === 'hex') {
            return is_int($value) || ctype_digit($value)
                ? sprintf('%x', $value)
                : '[not integer]';
        }

        if ($format === 'count') {
            return is_array($value) || is_a($value, Countable::class)
                ? count($value)
                : '[not countable]';
        }

        throw new LogicException("Unknown format »{$format}«");
    }

    /**
     * Format a value for being displayed as a string in an error message.
     *
     * @param mixed $value
     *
     * @return string
     */
    public static function escape($value)
    {
        if (is_scalar($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (is_callable($value)) {
            return sprintf('Callable (%s)', static::formatCallback($value));
        }

        if (is_resource($value)) {
            return sprintf('%s (%s)', $value, get_resource_type($value));
        }

        if (is_object($value)) {
            return sprintf('Object (%s)', get_class($value));
        }

        if (is_array($value)) {
            return sprintf('Array (%d entries)', count($value));
        }

        if (is_null($value)) {
            return 'null';
        }

        throw new RuntimeException(sprintf(
            'Unknown type: %s',
            gettype($value)
        ));
    }

    /**
     * Get the callback as a string.
     *
     * @param mixed $callback
     *
     * @return string
     */
    public static function formatCallback($callback)
    {
        if (is_string($callback)) {
            return $callback;
        }

        if (is_array($callback)) {
            list($classOrObject, $methodName) = $callback;

            return sprintf(
                '%s::%s',
                is_string($classOrObject) ? $classOrObject : get_class($classOrObject),
                $methodName
            );
        }

        if (is_a($callback, Closure::class)) {
            return '{closure}';
        }

        if (is_object($callback)) {
            return sprintf('%s::__invoke', get_class($callback));
        }

        return '{unknown}';
    }

    /**
     * Ensure that a value has a given type or class.
     *
     * @param mixed           $value The value to check
     * @param string|string[] $types Value must have at least one of the declared types
     * @param string|null     $error Error message to throw if the value was not correct
     *
     * @return $value
     *
     * @throws InvalidArgumentException if $value is not of the correct type
     */
    public static function mustBeA($value, $types, $error = null)
    {
        if (is_string($types)) {
            return static::mustBeA($value, explode('|', $types));
        }

        if (!is_array($types)) {
            throw new InvalidArgumentException(sprintf(
                '$types must be a string or an array of strings. %s given',
                ucfirst(gettype($types))
            ));
        }

        foreach ($types as $type) {
            $type = trim(strtolower($type));

            if ($type === 'callable' && is_callable($value)) {
                return $value;
            }

            if (strtolower(gettype($value)) === $type) {
                return $value;
            }

            if (is_a($value, $type)) {
                return  $value;
            }
        }

        if (count($types) === 1) {
            throw new InvalidArgumentException($error ? $error : sprintf(
                'The given value must be a %s',
                $types[0]
            ));
        }

        throw new InvalidArgumentException($error ? $error : sprintf(
            'The given value must be one of [%s]',
            implode(', ', $types)
        ));
    }
}
