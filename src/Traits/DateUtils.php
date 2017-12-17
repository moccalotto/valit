<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Traits;

use DateTime;
use Exception;
use DateTimeInterface;
use InvalidArgumentException;

trait DateUtils
{
    /**
     * The current time (if overridden).
     *
     * If $now is NULL, we have not overridden the current time.
     *
     * @var DateTimeInterface
     */
    protected $now;

    /**
     * In order to facilitate testing, we must be able to lock/override the "now" datetime.
     *
     * @param DateTimeInterface $now
     */
    public function overrideNow(DateTimeInterface $now)
    {
        $this->now = $now;
    }

    /**
     * Get the current DateTime.
     *
     * If the DateTime that represents the current time. If it was overridden with the overrideNow() method,
     * we return that DateTime instead.
     *
     * @return DateTimeInterface
     */
    public function now()
    {
        return $this->now ?: new DateTime();
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
    public function dt($candidate, $format = null)
    {
        if (is_a($candidate, DateTimeInterface::class)) {
            return $candidate;
        }

        if (is_int($candidate)) {
            return DateTime::createFromFormat('U', $candidate);
        }

        if (!is_string($candidate)) {
            throw new InvalidArgumentException('Candidate must be an int, a string or a DateTime');
        }

        if ($candidate === '') {
            throw new InvalidArgumentException('Candidate cannot be an empty string');
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
            throw new InvalidArgumentException(sprintf(
                'Candidate could be parsed as a datetime via the format "%s"',
                $format
            ), 0, $e);
        }

        if ($format && $dt->format($format) !== $candidate) {
            throw new InvalidArgumentException(sprintf(
                'Candidate could be parsed as a datetime via the format "%s"',
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
    public function canParse($candidate, $format = null)
    {
        try {
            $this->dt($candidate, $format);
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
     * @param DateTimeInterface $a,
     * @param DateTimeInterface $b
     *
     * @return float the number of seconds between $a and $b
     */
    public function compare(DateTimeInterface $a, DateTimeInterface $b)
    {
        $a = (float) $a->format('U.u');
        $b = (float) $b->format('U.u');

        return $a - $b;
    }
}
