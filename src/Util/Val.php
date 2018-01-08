<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit\Util;

use Closure;
use DateTime;
use Exception;
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
    public static function stringable($value)
    {
        return is_string($value)
            || is_int($value)
            || is_float($value)
            || is_object($value) && method_exists($value, '__toString');
    }

    /**
     * Can the given value be coerced into a number?
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function numeric($value)
    {
        $regex = '/^[-+]?[0-9]*\.?[0-9]+$/';

        return is_int($value)
            || is_float($value)
            || (static::stringable($value) && preg_match($regex, $value));
    }

    /**
     * Can value be traversed (is it iterable) ?
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function iterable($value)
    {
        return is_array($value)
            || is_a($value, Traversable::class);
    }

    /**
     * Can value be accessed as an array?
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function arrayable($value)
    {
        return is_array($value)
            || is_a($value, 'ArrayAccess');
    }

    /**
     * Can we count the elements in $value?
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function countable($value)
    {
        return is_array($value)
            || is_a($value, Countable::class);
    }

    /**
     * Can the value be converted to an integer without loss of information?
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function intable($value)
    {
        if (is_int($value)) {
            return true;
        }

        if (is_float($value)) {
            return $value == intval($value);
        }

        if (static::stringable($value)) {
            return floatval($value) == intval($value);
        }

        return false;
    }

    /**
     * Can the value be thrown via the throw keyword?
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function throwable($value)
    {
        return is_a($value, 'Exception')
            || is_a($value, 'Throwable');
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
        return (string) static::mustBe($value, 'stringable', $error);
    }

    /**
     * Coerce a value to to an array.
     *
     * Throw an exception if not possible.
     *
     * Arrays and instances of Traversable can be converted to array.
     *
     * @param mixed       $value
     * @param string|null $error Error message to throw if the value could not be converted
     *
     * @return string
     *
     * @throws InvalidArgumentException if $value could not be "nicely" converted to string
     */
    public static function toArray($value, $error = null)
    {
        static::mustBe($value, 'iterable', $error);

        return is_array($value)
            ? $value
            : iterator_to_array($value);
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
        if (is_null($error)) {
            $error = sprintf('The given %s could not be converted to an integer', gettype($value));
        }

        static::mustBe($value, ['intable'], $error);

        return intval($value);
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
        $strval = static::toString($value, $error);

        static::mustBe($strval, ['numeric']);

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
     * Create a closure from a callable.
     *
     * @param callable $callable
     *
     * @return Closure
     */
    public static function toClosure($callable)
    {
        if (is_callable('Closure::fromCallable')) {
            return Closure::fromCallable($callable);
        }

        return function () use ($callable) {
            return call_user_func_array($callable, func_get_args());
        };
    }

    /**
     * Count the elements in an array, a Countable or a Traversable.
     *
     * @param mixed $value
     *
     * @return int
     */
    public static function count($value)
    {
        static::mustBe($value, ['iterable', 'countable']);

        if (static::countable($value)) {
            return count($value);
        }

        if (static::iterable($value)) {
            return iterator_count($value);
        }

        // This code should not be reachable.
        throw new LogicException(sprintf('count() failed to understand the given %s', gettype($value)));
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

        if ($format === 'imploded') {
            return static::iterable($value)
                ? implode(', ', static::map($value, '::escape'))
                : '[not iterable]';
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
            return static::is($value, ['countable', 'iterable'])
                ? (string) static::count($value)
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

        if (is_a($value, 'DateTimeInterface')) {
            return sprintf(
                '%s (%s)',
                get_class($value),
                $value->format(DateTime::RFC3339)
            );
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
     * @param mixed                   $value The value to check
     * @param string|string[]         $types Value must have at least one of the declared types
     * @param string|\Exception|\null $error Error message to throw if the value was not correct
     *
     * @return $value
     *
     * @throws InvalidArgumentException if $value is not of the correct type
     */
    public static function mustBe($value, $types, $error = null)
    {
        if (!static::is($error, ['null', 'string', 'Exception'])) {
            throw new LogicException('$error must be null, a string or an instance of Exception');
        }

        if (static::is($value, $types)) {
            return $value;
        }

        if (is_a($error, 'Exception')) {
            throw $error;
        }

        if (is_string($error)) {
            throw new InvalidArgumentException($error);
        }

        if (is_string($types)) {
            $types = static::explodeAndTrim('|', $types);
        }

        if (count($types) === 1) {
            throw new InvalidArgumentException(sprintf('The given value must be a %s', $types[0]));
        }

        throw new InvalidArgumentException(sprintf('The given value must be one of [%s]', implode(', ', $types)));
    }

    /**
     * Normalize a string of separated words into an array.
     *
     * If $types is already an array, it will merely be trimmed.
     *
     * @param string          $separator
     * @param string|string[] $types
     *
     * @return string[]
     */
    public static function explodeAndTrim($separator, $types)
    {
        // we must use array syntax when calling mustBe() to avoid cyclic calls.
        static::mustBe($separator, ['string'], '$separator must be a string');
        static::mustBe($types, ['string', 'string[]'], '$types must be a string or an array of strings');

        if (is_string($types)) {
            $types = explode($separator, $types);
        }

        return static::map($types, 'trim');
    }

    /**
     * Check if a value has a given type or class.
     *
     * $types can be a string with a type name such as:
     *  'int', 'float', 'bool', 'array' or even pseudy
     *  types such as 'callable', 'iterable', 'countable',
     *  'stringable' and 'arrayable'.
     *
     * It can also be a fully qualified class name such as
     *  'Valit\Check'
     *
     * It can also be an array of the above types. If it is an
     * array, then $value can be any of the given values.
     * Example:
     *  ['int', 'DateTimeInterface']
     *
     * It can also be a string with many types separated by a pipe `|` characer.
     * Example:
     *  'string|DateTimeInterface', 'int | float'
     *
     * @param mixed           $value The value to check
     * @param string|string[] $types Value must have at least one of the declared types
     *
     * @return bool
     *
     * @throws InvalidArgumentException if $types is not a string or an array of strings
     */
    public static function is($value, $types)
    {
        if (is_string($types)) {
            $types = static::explodeAndTrim('|', $types);
        }

        foreach ($types as $type) {
            // check if type is one of: resource, double, integer, string, object, array, bool, null
            if (gettype($value) === $type) {
                return true;
            }

            // check if class equals $type
            if (is_a($value, $type)) {
                return true;
            }

            if ($type === 'null' && is_null($value)) {
                return true;
            }

            if ($type === 'scalar' && is_scalar($value)) {
                return true;
            }

            if ($type === 'callable' && is_callable($value)) {
                return true;
            }

            if ($type === 'int' && is_int($value)) {
                return true;
            }

            if ($type === 'bool' && is_bool($value)) {
                return true;
            }

            if ($type === 'numeric' && static::numeric($value)) {
                return true;
            }

            if ($type === 'float' && is_float($value)) {
                return true;
            }

            if ($type === 'iterable' && static::iterable($value)) {
                return true;
            }

            if ($type === 'countable' && static::countable($value)) {
                return true;
            }

            if ($type === 'stringable' && static::stringable($value)) {
                return true;
            }

            if ($type === 'intable' && static::intable($value)) {
                return true;
            }

            if ($type === 'arrayable' && static::arrayable($value)) {
                return true;
            }

            if ($type === 'throwable' && static::throwable($value)) {
                return true;
            }

            if ($type === 'container'
                && static::arrayable($value)
                && static::countable($value)
                && static::iterable($value)
            ) {
                return true;
            }

            if ($type === 'nan' && is_nan($value)) {
                return true;
            }

            if ($type === 'inf' && is_infinite($value)) {
                return true;
            }

            // check for array types such as string[], float[], DateTime[], etc.
            if (substr($type, -2) === '[]' && static::isArrayOf($value, substr($type, 0, -2))) {
                return true;
            }

            if ($type === 'mixed') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if $value is an 0-indexed continuous array of the given type.
     *
     * All keys from 0 to count($value) must exist.
     * All values must be of type $type.
     *
     * @param mixed  $value
     * @param string $type
     *
     * @return bool
     */
    public static function isArrayOf($value, $type)
    {
        if (!is_array($value)) {
            return false;
        }

        $count = count($value);

        for ($i = 0; $i < $count; $i++) {
            if (!isset($value[$i])) {
                return false;
            }

            if (!static::is($value[$i], $type)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Map an iterable variable.
     *
     * Example:
     *
     * ```php
     * // the two lines below are equivalent:
     * $escaped = Val::map($array, '::escape');
     * $escaped = Val::map($array, 'Valit\Util\Val::escape');
     *
     * $integers = Val::map($array, 'intval');  // php version
     * $integers = Val::map($array, '::toInt'); // robust version
     * ```
     *
     * @param iterable        $iterable The array (or traversable) to be mapped.
     * @param string|callable $callable A callable or a string starting with two colons.
     *                                  If it is a string with two colons, it is actually
     *                                  a short-hand for calling a static function in this class.
     *                                  For instance, if $callable is '::escape' then
     *                                  it is the same as if $callable was 'Valit\Util\Val::escape'.
     *
     * @return array
     */
    public static function map($iterable, $callable)
    {
        if (static::stringable($callable) && substr($callable, 0, 2) === '::') {
            $callable = [static::class, substr($callable, 2)];
        }

        static::mustBe($iterable, ['iterable']);
        static::mustBe($callable, ['callable']);

        $result = [];

        foreach ($iterable as $key => $value) {
            $result[$key] = $callable($value);
        }

        return $result;
    }

    /**
     * Return the first argument that is not null.
     *
     * Similar to the php 7 null-coalesce operator.
     *
     * @param mixed $args,... The arguments
     *
     * @return mixed
     */
    public static function firstNotNull()
    {
        foreach (func_get_args() as $arg) {
            if ($arg !== null) {
                return $arg;
            }
        }

        return null;
    }
}
