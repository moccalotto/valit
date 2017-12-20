<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use Valit\Traits;
use DateTimeInterface;
use InvalidArgumentException;
use Valit\Contracts\CheckProvider;
use Valit\Result\AssertionResult as Result;

/**
 * Check that dates for validity.
 *
 * Check that date strings are parsable into DateTimes with expected results.
 * Check that dates-times are within certain ranges.
 */
class DateCheckProvider implements CheckProvider
{
    use Traits\DateUtils,
        Traits\ProvideViaReflection;

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
    public function checkDateParsable($value, $format = null)
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
            throw new InvalidArgumentException('$against must be a DateTimeInterface object');
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
            throw new InvalidArgumentException('$against must be a DateTimeInterface object');
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
