<?php

/*
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
use Moccalotto\Valit\Result;
use InvalidArgumentException;
use Moccalotto\Valit\Contracts\CheckProvider;
use Moccalotto\Valit\Traits\ProvideViaReflection;

class DateCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Is the candidate value can be treated as a date.
     *
     * @param string|DateTime $candidate
     *
     * @return bool
     */
    protected function canParse($candidate)
    {
        try {
            $this->dt($candidate);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Convert the candidate value into a carbon date object.
     *
     * @param mixed $candidate
     * @param string $format
     *
     * @return DateTime
     */
    protected function dt($candidate, $format = null)
    {
        if ($candidate instanceof DateTime) {
            return $candidate;
        }

        if (is_int($candidate)) {
            return DateTime::createFromFormat('U', $candidate);
        }

        if ($format && is_string($candidate)) {
            return DateTime::createFromFormat($format, $candidate);
        }

        if (is_string($candidate)) {
            return new DateTime($candidate);
        }
    }

    /**
     * Check if $value is a string containing valid xml.
     *
     * @Check(["isValidXml", "validXml"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checksDateParsable($value)
    {
        return new Result(
            $this->canParse($value),
            '{name} must be a canParse as a date'
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

    public function checkSameDayAs($value, $against)
    {
        $againstDate = $this->dt($against)->format('Y-m-d');

        $success = $this->canParse($value)
            && $this->dt($value)->format('Y-m-d') === $againstDate;

        return new Result($success, '{name} must be on the {0:raw}', [$againstDate]);
    }

    public function checkWeekdaySameAs($value, $against)
    {
        $success = $this->canParse($value)
            && $this->dt($value)->format('N') == $this->dt($against)->format('N');

        return new Result($success, '{name} must be a on a {0:raw}', [$this->dt($against)->format('l')]);
    }
}
