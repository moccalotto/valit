<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Util;

use DateTime;
use Exception;
use DateInterval;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * Provide functionality to parse dates that have been string-encoded.
 *
 * Also allow faking the current time for test purposes.
 */
abstract class Date
{
    /**
     * The current time (if overridden).
     *
     * If $now is NULL, we have not overridden the current time.
     *
     * @var DateTimeInterface
     */
    protected static $now;

    /**
     * In order to facilitate testing, we must be able to lock/override the "now" datetime.
     *
     * @param DateTimeInterface|null $now
     */
    public static function overrideNow(DateTimeInterface $now = null)
    {
        static::$now = $now;
    }

    /**
     * Get the current DateTime.
     *
     * If the DateTime that represents the current time. If it was overridden with the overrideNow() method,
     * we return that DateTime instead.
     *
     * @return DateTimeInterface
     */
    public static function now()
    {
        return static::$now ?: new DateTime();
    }

    /**
     * Create a DateTime from a unix timestamp.
     *
     * Robust against negative timestamps and floating point timestamps.
     *
     * @param int|float $timestamp
     *
     * @return DateTime
     */
    public static function fromUnixTimestamp($timestamp)
    {
        $interval = new DateInterval('PT0S');
        $parts = explode('.', $timestamp);
        $interval->s = (int) $parts[0];
        if (isset($parts[1])) {
            $interval->f = (int) $parts[1];
        }
        $result = DateTime::createFromFormat('U', 0);
        $result->add($interval);

        return $result;
    }

    /**
     * Convert the candidate value into a DateTime object.
     *
     * @param string|int|DateTimeInterface $candidate
     * @param string|null                  $format
     *
     * @return DateTimeInterface
     *
     * @throws InvalidArgumentException if $candidate is not string, int or DateTime, or if it could not be parsed
     */
    public static function parse($candidate, $format = null)
    {
        if (is_a($candidate, DateTimeInterface::class)) {
            return $candidate;
        }

        if (is_int($candidate) || is_float($candidate)) {
            return static::fromUnixTimestamp($candidate);
        }

        if (!is_string($candidate)) {
            throw new InvalidArgumentException(sprintf(
                'Cannot parse date. The candidate must be an int, float, string or a DateTimeInterface. %s given',
                Val::escape($candidate)
            ));
        }

        if ($candidate === '') {
            throw new InvalidArgumentException('Cannot parse date. The candidate cannot be an empty string');
        }

        // The format must either be null or a string.
        if (!(is_null($format) || is_string($format))) {
            throw new InvalidArgumentException(sprintf(
                'The format must either be NULL or a string. %s given',
                ucfirst(gettype($format))
            ));
        }

        try {
            // Use the given format to parse the DateTime (createFromFormat)
            // otherwise try and infer the format (via the constructor).
            $dt = DateTime::createFromFormat((string) $format, $candidate) ?: new DateTime($candidate);
        } catch (Exception $e) {
            throw new InvalidArgumentException('Cannot parse the given datetime', 0, $e);
        }

        if ($format && $dt->format($format) !== $candidate) {
            throw new InvalidArgumentException(sprintf(
                'Cannot parse parse date via the format "%s"',
                $format
            ));
        }

        return $dt;
    }

    /**
     * Is the candidate value can be treated as a date.
     *
     * @param string|DateTimeInterface $candidate candidate date
     * @param string|null              $format    The format to use. @see http://php.net/manual/en/class.datetime.php
     *
     * @return bool
     */
    public static function canParse($candidate, $format = null)
    {
        try {
            static::parse($candidate, $format);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Compare two datetimes in a PHP-version agnostic way.
     *
     * PHP 5.* does not account for microseconds when comparing two datetimes.
     * We therefore convert the datetimes into floating point timestamps
     * and then compare them as normal floating point numbers.
     *
     * @param mixed $a A parseable datetime
     * @param mixed $b A parseable datetime
     *
     * @return float the number of seconds between $a and $b
     */
    public static function compare($a, $b)
    {
        $a = (float) static::parse($a)->format('U.u');
        $b = (float) static::parse($b)->format('U.u');

        return $a - $b;
    }

    /**
     * Compare two dates.
     *
     * @param string $comparison The comparison method; one of 'before', 'beforeOrAt', 'at', 'after', 'afterOrAt'
     * @param mixed  $a          A parseable datetime
     * @param mixed  $b          A parseable datetime
     *
     * @return bool
     *
     * @throws InvalidArgumentException if $comparison is invalid or $a or $b could not be parsed
     */
    public static function comparison($comparison, $a, $b)
    {
        $a = (float) static::parse($a)->format('U.u');
        $b = (float) static::parse($b)->format('U.u');

        if ($comparison === 'before') {
            return $a < $b;
        }
        if ($comparison === 'beforeOrAt') {
            return $a <= $b;
        }
        if ($comparison === 'at') {
            return $a == $b;
        }
        if ($comparison === 'after') {
            return $a > $b;
        }
        if ($comparison === 'afterOrAt') {
            return $a > $b;
        }
        throw new InvalidArgumentException(
            'first argument must be one of "before", "beforeOrAt", "at", "after", "afterOrAt"'
        );
    }
}
