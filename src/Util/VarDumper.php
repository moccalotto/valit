<?php

namespace Valit\Util;

use Countable;
use LogicException;
use RuntimeException;

class VarDumper
{
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
            return 'Callable';
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
}
