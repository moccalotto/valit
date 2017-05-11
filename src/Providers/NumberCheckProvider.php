<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 *
 * @codingStandardsIgnoreFile
 */

namespace Moccalotto\Valit\Providers;

use InvalidArgumentException;
use Moccalotto\Valit\Result;
use Moccalotto\Valit\Contracts\CheckProvider;
use Moccalotto\Valit\Traits\ProvideViaReflection;

class NumberCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Ensure that a value can be parased into a float or int.
     *
     * @param mixed $value
     *
     * @throws InvalidArgumentException if $value is not numeric
     */
    protected function assertNumeric($value)
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Check argument must be numeric');
        }
    }

    /**
     * Find the greatest common divisor between two numbers.
     *
     * @param numeric $a
     * @param numeric $b
     *
     * @return int
     */
    protected function gcd($a, $b)
    {
        list($a, $b) = [
            (int) min($a, $b),
            (int) max($a, $b),
        ];

        while ($b !== 0) {
            $temp = $a;
            $a = $b;
            $b = $temp % $b;
        }

        return $a;
    }

    /**
     * Check that $value is numeric in a php-version consistent way.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function numeric($value)
    {
        $regex = '/^[-+]?[0-9]*\.?[0-9]+$/';

        return is_int($value)
            || is_float($value)
            || (is_string($value) && preg_match($regex, $value));
    }

    /**
     * Check that $value is numeric.
     *
     * @Check(["numeric", "isNumeric"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkNumeric($value)
    {
        return new Result($this->numeric($value), '{name} must be numeric');
    }

    /**
     * Check that $value is a real number.
     *
     * @Check(["realNumber", "isRealNumber"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkRealNumber($value)
    {
        $success = $this->numeric($value) && is_finite(floatval($value));

        return new Result($success, '{name} must be a real number');
    }

    /**
     * Check that $value is a natural number.
     *
     * @Check(["isNaturalNumber", "naturalNumber", "isWholeNumber", "wholeNumber"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkNaturalNumber($value)
    {
        $success = $this->numeric($value)
            && is_finite($value)
            && (ceil($value) === floor($value));

        return new Result($success, '{name} must be a natural number');
    }

    /**
     * Check if $value > $against.
     *
     * @Check(["greaterThan", "isGreaterThan", "gt"])
     *
     * @param mixed   $value
     * @param numeric $against
     *
     * @return Result
     */
    public function checkGreaterThan($value, $against)
    {
        $this->assertNumeric($against);

        $success = is_numeric($value) && $value > $against;

        return new Result($success, '{name} must be greater than {0}', [$against]);
    }

    /**
     * Check if $value >= $against.
     *
     * @Check(["greaterThanOrEqual", "isGreaterThanOrEqual", "gte"])
     *
     * @param mixed   $value
     * @param numeric $against
     *
     * @return Result
     */
    public function checkGreaterThanOrEqual($value, $against)
    {
        $this->assertNumeric($against);

        $success = is_numeric($value) && $value >= $against;

        return new Result($success, '{name} must be greater than or equal to {0}', [$against]);
    }

    /**
     * Check if $value < $against.
     *
     * @Check(["lessThan", "isLessThan", "lowerThan", "isLowerThan", "lt"])
     *
     * @param mixed   $value
     * @param numeric $against
     *
     * @return Result
     *
     * @throws InvalidArgumentException if $against is not numeric
     */
    public function checkLessThan($value, $against)
    {
        $this->assertNumeric($against);

        $success = is_numeric($value) && $value < $against;

        return new Result($success, '{name} must be less than {0}', [$against]);
    }

    /**
     * Check if $value <= $against.
     *
     * @Check(["lessThanOrEqual", "isLessThanOrEqual", "lowerThanOrEqual", "isLowerThanOrEqual", "lte"])
     *
     * @param mixed   $value
     * @param numeric $against
     *
     * @return Result
     *
     * @throws InvalidArgumentException if $against is not numeric
     */
    public function checkLessThanOrEqual($value, $against)
    {
        $this->assertNumeric($against);

        $success = is_numeric($value) && $value <= $against;

        return new Result($success, '{name} must be less than {0}', [$against]);
    }

    /**
     * Check if $value is extremely close to $against.
     *
     * @Check(["closeTo", "isCloseTo", "floatEquals", "isFloatEqualTo"])
     *
     * @param mixed   $value
     * @param numeric $against
     * @param numeric $epsilon
     *
     * @return Result
     *
     * @throws InvalidArgumentException if $against is not numeric
     * @throws InvalidArgumentException if $epsilon is not numeric
     */
    public function checkFloatEqual($value, $against, $epsilon = 0.00001)
    {
        $this->assertNumeric($against);
        $this->assertNumeric($epsilon);

        if (!is_finite($epsilon)) {
            throw new InvalidArgumentException('Epsilon must be a real number');
        }

        $testable = is_numeric($value) && !is_nan($value);
        $success = $testable ? abs(abs($value) - abs($against)) <= $epsilon : false;

        return new Result($success, '{name} must equal {0:float} with a margin of error of {1:float}', [
            sprintf('%g', $against),
            sprintf('%g', $epsilon),
        ]);
    }

    /**
     * Check that $value is an odd integer.
     *
     * @Check(["odd", "isOdd"])
     *
     * @return Result
     */
    public function checkOdd($value)
    {
        $success = is_numeric($value)
            && (float) $value == (int) $value
            && ($value & 1) === 1;

        return new Result($success, '{name} must be an odd integer');
    }

    /**
     * Check that $value is an even integer.
     *
     * @Check(["even", "isEven"])
     *
     * @return Result
     */
    public function checkEven($value)
    {
        $success = is_numeric($value)
            && (float) $value == (int) $value
            && ($value & 1) === 0;

        return new Result($success, '{name} must be an even integer');
    }

    /**
     * Check if $value is positive.
     *
     * @Check(["positive", "isPositive"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkPositive($value)
    {
        return $this->checkGreaterThan($value, 0);
    }

    /**
     * Check if $value is negative.
     *
     * @Check(["negative", "isNegative"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkNegative($value)
    {
        return $this->checkLessThan($value, 0);
    }

    /**
     * Check if $value is prime relative to $against.
     *
     * @Check(["isPrimeRelativeTo", "primeRelativeTo", "isRelativePrime", "relativePrime", "isCoprimeTo", "coprimeTo"])
     *
     * @param mixed $value
     * @param int   $against
     *
     * @return Result
     */
    public function checkPrimeRelativeTo($value, $against)
    {
        $this->assertNumeric($against);

        if (intval($against) != floatval($against)) {
            throw new InvalidArgumentException('$against must be a finite natural number');
        }

        $success = ((float) $value == (int) $value)
            && $this->gcd($value, $against) === 1;

        return new Result($success, '{name} must be prime relative to {0}', [$against]);
    }

    /**
     * Check if $value is prime relative to $against.
     *
     * @Check(["isDivisibleBy", "divisibleBy", "dividesBy"])
     *
     * @param mixed $value
     * @param int   $against
     *
     * @return Result
     */
    public function checkDivisibleBy($value, $against)
    {
        $this->assertNumeric($against);

        if ($against == 0.0 || !is_finite($against)) {
            throw new InvalidArgumentException('$against must be a finite, non-zero number');
        }

        $success = is_numeric($value)
            && is_finite($value)
            && fmod($value, $against) === 0.0;

        return new Result($success, '{name} must be divisible by {0}', [$against]);
    }
}
