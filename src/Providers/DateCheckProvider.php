<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Moccalotto\Valit\Providers;

use DateTime;
use Exception;
use DateTimeInterface;
use InvalidArgumentException;
use Moccalotto\Valit\Contracts\CheckProvider;
use Moccalotto\Valit\Result;
use Moccalotto\Valit\Traits\ProvideViaReflection;

/**
 * Check that dates for validity.
 *
 * Check that date strings are parsable into DateTimes with expected results.
 * Check that dates-times are within certain ranges.
 */
class DateCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
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
        $this->now = clone $now;
    }

    /**
     * Get the current DateTime.
     *
     * If the DateTime that represents the current time. If it was overridden with the overrideNow() method,
     * we return that DateTime instead.
     */
    protected function now()
    {
        return $this->now ? clone $this->now : new DateTime();
    }

    /**
     * Is the candidate value can be treated as a date.
     *
     * @param string|DateTimeInterface $candidate candidate date
     * @param string|null              $format    The format to use. @see http://php.net/manual/en/class.datetime.php
     *
     * @return bool
     */
    protected function canParse($candidate, $format = null)
    {
        try {
            $this->dt($candidate, $format);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Convert the candidate value into a DateTime object.
     *
     * @param string|int|DateTimeInterface $candidate
     * @param string|null                  $format
     *
     * @return DateTime
     *
     * @throws InvalidArgumentException if $candidate is not string, int or DateTime, or if it could not be parsed
     */
    protected function dt($candidate, $format = null)
    {
        if ($candidate instanceof DateTimeInterface) {
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

        try {
            /**
             * Use the given format to parse the DateTime (createFromFormat)
             * otherwise try and infer the format (via the constructor).
             *
             * @var DateTime
             */
            $dt = DateTime::createFromFormat((string) $format, $candidate) ?: new DateTime($candidate);
        } catch (Exception $e) {
            // new DateTime can throw Exception - we only want to throw InvalidArgumentException
            // so we catch it.
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
     * Compare two datetimes in a PHP-version agnostic way.
     *
     * PHP 5.* does not account for microseconds when comparing two datetimes.
     * We therefore convert the datetimes into floating floating point timestamps
     * and compare them as normal floats.
     *
     * @param DateTimeInterface $a,
     * @param DateTimeInterface $b
     *
     * @return float the number of seconds between $a and $b
     */
    protected function compare(DateTimeInterface $a, DateTimeInterface $b)
    {
        $aFloat = (float) $a->format('U.u');
        $bFloat = (float) $b->format('U.u');

        return $aFloat - $bFloat;
    }

    /**
     * Check if $value is a string containing a parseable date.
     *
     * @Check(["isParsableDate", "parsableDate", "isDateString", "dateString"])
     *
     * @param mixed  $value
     * @param string $format
     *
     * @return Result
     */
    public function checkDateParsable($value, $format)
    {
        return new Result(
            $this->canParse($value, $format),
            '{name} must be a parsable date'
        );
    }

    /**
     * Check if $value is a date after $against.
     *
     * @Check(["isDateAfter", "occursAfter", "dateAfter", "laterThan", "isLaterThan"])
     *
     * @param mixed             $value
     * @param DateTimeInterface $against
     *
     * @return Result
     */
    public function checkDateAfter($value, $against)
    {
        if (!$against instanceof DateTimeInterface) {
            throw new InvalidArgumentException('$against must be a DateTime object');
        }
        $success = $this->canParse($value)
            && $this->compare($this->dt($value), $against) > 0;

        return new Result($success, '{name} must be a date after {0:raw}', [$against]);
    }

    /**
     * Check if $value is a date after $against.
     *
     * @Check(["isDateBefore", "occursBefore", "dateBefore", "earlierThan", "isEarlierThan"])
     *
     * @param mixed             $value
     * @param DateTimeInterface $against
     *
     * @return Result
     */
    public function checkDateBefore($value, $against)
    {
        if (!$against instanceof DateTimeInterface) {
            throw new InvalidArgumentException('$against must be a DateTime object');
        }

        $success = $this->canParse($value)
            && $this->compare($this->dt($value), $against) < 0;

        return new Result($success, '{name} must be a date before {0:raw}', [$against]);
    }

    /**
     * Check if $value is a date in the past.
     *
     * @Check(["dateInThePast", "isDateInThePast"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkInThePast($value)
    {
        $success = $this->canParse($value) &&
            $this->compare($this->dt($value), $this->now()) < 0;

        return new Result($success, '{name} must be a date in the past');
    }

    /**
     * Check if $value is a date in the past.
     *
     * @Check(["dateInTheFuture", "isDateInTheFuture"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkInTheFuture($value)
    {
        $success = $this->canParse($value) &&
            $this->compare($this->dt($value), $this->now()) > 0;

        return new Result($success, '{name} must be a future date');
    }

    /**
     * Check if $value is a date where the time-component is 00:00:00.
     *
     * @Check(["dateTimeAtMidnight", "isDateTimeAtMidnight", "isDateOnly", "dateOnly"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkAtMidnight($value)
    {
        $success = $this->canParse($value) && $this->dt($value)->format('His.u') == 0;

        return new Result($success, '{name} must be a datetime at midnight');
    }

    public function checkAtNoon($value)
    {
        $success = $this->canParse($value) && $this->dt($value)->format('His.u') == 120000;

        return new Result($success, '{name} must be a datetime at noon');
    }

    public function checkSameDateAs($value, $against)
    {
        $againstDate = $this->dt($against)->format('Y-m-d');

        $success = $this->canParse($value)
            && $this->dt($value)->format('Y-m-d') === $againstDate;

        return new Result($success, '{name} must be on the {0:raw}', [$againstDate]);
    }

    public function checkWeekdaySameAs($value, $against)
    {
        $success = $this->canParse($value)
            && $this->dt($value)->format('N') === $this->dt($against)->format('N');

        return new Result($success, '{name} must be a on a {0:raw}', [$this->dt($against)->format('l')]);
    }

    public function checkBirthday($value, $against)
    {
        $success = $this->canParse($value)
            && $this->dt($against)->format('md') === $this->dt($value)->format('md');

        return new Result($success, '{name} must be on the {0:raw}', [$this->dt($against)->format('F dS')]);
    }
}
