<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use Valit\Util\Val;
use Valit\Util\Date;
use DateTimeInterface;
use InvalidArgumentException;
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;
use Valit\Result\AssertionResult as Result;

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
     * Check if $value is a string containing a parseable date.
     *
     * The `$format` parameter can be null or a string.
     * If it is null, we will attempt to parse the $value as a DateTime via
     * PHPs built-in inference. If `$format` is a string, it will be used
     * to define the format of the $value.
     * See <http://php.net/manual/datetime.createfromformat.php> for more info.
     *
     * @Check(["isParsableDate", "parsableDate", "isDateString", "dateString"])
     *
     * @param mixed       $value
     * @param string|null $format
     *
     * @return Result
     *
     * @see http://php.net/manual/en/datetime.createfromformat.php
     */
    public function checkDateParsable($value, $format = null)
    {
        return new Result(
            Date::canParse($value, $format),
            '{name} must be a parsable date'
        );
    }

    /**
     * Check if $value is a date after $against.
     *
     * The `$against` parameter can be one of:
     * - `DateTimeInterface`
     * - `int` The value will be treated as a UNIX timestamp.
     * - `float` The value will be treated as a UNIX timestamp with a sub-second component.
     * - `string` The value will be converted to a `DateTime` if possible via PHPs native date time inference.
     *
     * @Check(["isDateAfter", "occursAfter", "dateAfter", "laterThan", "isLaterThan"])
     *
     * @param mixed                    $value
     * @param DateTimeInterface|string $against
     *
     * @return Result
     */
    public function checkDateAfter($value, $against)
    {
        $againstDate = Date::parse($against);

        $success = Date::canParse($value)
            && Date::compare(Date::parse($value), $againstDate) > 0;

        return new Result($success, '{name} must be a date after {0:raw}', [$against]);
    }

    /**
     * Check if $value is a date after $against.
     *
     * The `$against` parameter can be one of:
     * - `DateTimeInterface`
     * - `int` The value will be treated as a UNIX timestamp.
     * - `float` The value will be treated as a UNIX timestamp with a sub-second component.
     * - `string` The value will be converted to a `DateTime` if possible via PHPs native date time inference.
     *
     * @Check(["isDateBefore", "occursBefore", "dateBefore", "earlierThan", "isEarlierThan"])
     *
     * @param mixed $value
     * @param mixed $against
     *
     * @return Result
     */
    public function checkDateBefore($value, $against)
    {
        $againstDate = Date::parse($against);

        $success = Date::canParse($value)
            && Date::compare(Date::parse($value), $againstDate) < 0;

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
        $success = Date::canParse($value)
            && Date::compare(Date::parse($value), Date::now()) < 0;

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
        $success = Date::canParse($value)
            && Date::compare(Date::parse($value), Date::now()) > 0;

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
        $success = Date::canParse($value)
            && Date::parse($value)->format('His.u') == 0;

        return new Result($success, '{name} must be a datetime at midnight');
    }

    /**
     * Check if $value is a date where the time-component is 12:00:00.
     *
     * @Check(["dateTimeAtNoon", "isDateTimeAtNoon"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkAtNoon($value)
    {
        $success = Date::canParse($value)
            && Date::parse($value)->format('His.u') == 120000;

        return new Result($success, '{name} must be a datetime at noon');
    }

    /**
     * Check if $value is a datetime where the date-component is
     * the same as the date-component of $against.
     *
     * @Check(["sameDateAs", "isSameDateAs", "sameDayAs", "isSameDayAs"])
     *
     * @param mixed                    $value
     * @param string|DateTimeInterface $against
     *
     * @return Result
     */
    public function checkSameDateAs($value, $against)
    {
        $againstString = Date::parse($against)->format('Y-m-d');

        $success = Date::canParse($value)
            && Date::parse($value)->format('Y-m-d') === $againstString;

        return new Result($success, '{name} must be on the {0:raw}', [$againstString]);
    }

    /**
     * Check if $value is a datetime where the weekday-component is
     * the same as the weekday-component of $against.
     *
     * @Check(["sameDayOfWeek", "isSameDayOfWeek", "isDayOfWeek", "dayOfWeek"])
     *
     * @param mixed                    $value
     * @param string|DateTimeInterface $against
     *
     * @return Result
     */
    public function checkSameDayOfWeek($value, $against)
    {
        $success = Date::canParse($value)
            && Date::parse($value)->format('N') === Date::parse($against)->format('N');

        return new Result($success, '{name} must be a on a {0:raw}', [Date::parse($against)->format('l')]);
    }

    /**
     * Check if $value is a datetime where the day of month is $against.
     * The first day of the month is the 1st, i.e. days are 1-indexed.
     *
     * @Check(["isDayOfMonth", "dayOfMonth"])
     *
     * @param mixed $value
     * @param int   $against
     *
     * @return Result
     */
    public function checkDayOfMonth($value, $against)
    {
        $dayOfMonth = Val::toInt($against, '$against must be an integer');

        if ($dayOfMonth > 31 || $dayOfMonth < 1) {
            throw new InvalidArgumentException('$against must be an integer between 1 and 31');
        }

        $success = Date::canParse($value)
            && Date::parse($value)->format('j') == $dayOfMonth;

        return new Result($success, '{name} must be a on the {0:int}. day of the month', [$against]);
    }

    /**
     * Check if $value is a datetime where the day-month-component is
     * the same as the day-month-component of $against.
     * In other words, are the two dates "birthday-equivalent" of each other.
     *
     * For instance:
     *  '1987-12-01 23:30:00' is birthday equivalent of '1950-12-01 11:32:34'
     *  because they both occur in on december 1st.
     *
     * @Check(["isBirthdayEquivalentOf", "birthdatEquivalentOf", "sameDayAndMonth", "isSameDayAndMonth"])
     *
     * @param mixed                    $value
     * @param string|DateTimeInterface $against
     *
     * @return Result
     */
    public function checkBirthday($value, $against)
    {
        $againstString = Date::parse($against)->format('md');

        $success = Date::canParse($value)
            && Date::parse($value)->format('md') === $againstString;

        return new Result($success, '{name} must be on the {0:raw}', [Date::parse($against)->format('F dS')]);
    }
}
