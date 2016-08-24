<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace Moccalotto\Valit\Providers;

use DateTime;
use Exception;
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
     * Is the candidate value can be treated as a date.
     *
     * @param string|DateTime $candidate Candidate date.
     * @param string $format The format to use. @see http://php.net/manual/en/class.datetime.php
     *
     * @return bool
     */
    protected function canParse($candidate, $format)
    {
        try {
            $this->dt($candidate, $format);
        } catch (Exception $e) {
            error_log($e);

            return false;
        }

        return true;
    }

    /**
     * Convert the candidate value into a DateTime object.
     *
     * @param string|int|DateTime $candidate
     * @param string $format
     *
     * @return DateTime
     */
    protected function dt($candidate, $format)
    {
        if (empty($format) || !is_string($format)) {
            throw new InvalidArgumentException('Format must be a non-empty string');
        }

        if ($candidate instanceof DateTime) {
            return $candidate;
        }

        if (is_int($candidate)) {
            return DateTime::createFromFormat('U', $candidate);
        }

        if (is_string($candidate)) {
            /**
             * Use the given format to parse the DateTime (createFromFormat)
             * otherwise try and infer the format (via the constructor).
             * @var DateTime
             */
            $dt = DateTime::createFromFormat($format, $candidate)
                ?: new DateTime($candidate);

            if ($dt->format($format) === $candidate) {
                return $dt;
            };

            throw new InvalidArgumentException(sprintf(
                'Candidate could be parsed as a datetime via the format "%s"',
                $format
            ));
        }

        throw new InvalidArgumentException('Candidate must be an int, a string or a DateTime');
    }

    /**
     * Check if $value is a string containing valid xml.
     *
     * @Check(["isParsableDate", "parsableDate", "isDateString", "dateString"])
     *
     * @param mixed $value
     * @param string $format
     *
     * @return Result
     */
    public function checksDateParsable($value, $format)
    {
        return new Result(
            $this->canParse($value, $format),
            '{name} must be a parsable date'
        );
    }

    public function checkDateAfter($value, $against)
    {
        $success = $this->canParse($value)
            && $this->dt($value) > $this->dt($against);

        return new Result($success, '{name} must be a date after {0:raw}', [$against]);
    }

    public function checkDateBefore($value, $against)
    {
        $success = $this->canParse($value)
            && $this->dt($value) > $this->dt($against);

        return new Result($success, '{name} must be a date before {0:raw}', [$against]);
    }

    public function checkInThePast($value)
    {
        $success = $this->canParse($value) && $this->dt($value) < new DateTime();

        return new Result($success, '{name} must be a date in the past');
    }

    public function checkInTheFuture($value)
    {
        $success = $this->canParse($value) && $this->dt($value) > new DateTime();

        return new Result($success, '{name} must be a future date');
    }

    public function checkAtMidnight($value)
    {
        $success = $this->canParse($value) && $this->dt($value)->format('h:i:s') === '00:00:00';

        return new Result($success, '{name} must be a datetime at midnight');
    }

    public function checkAtNoon($value)
    {
        $success = $this->canParse($value) && $this->dt($value)->format('h:i:s') === '12:00:00';

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
