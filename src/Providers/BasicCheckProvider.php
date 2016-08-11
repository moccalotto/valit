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

use Moccalotto\Valit\Result;
use Moccalotto\Valit\Contracts\CheckProvider;
use Moccalotto\Valit\Traits\ProvideViaReflection;

class BasicCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check that $value is equals $against.
     *
     * @Check(["isIdenticalTo", "identicalTo", "sameAs", "isSameAs"])
     *
     * @param mixed $value
     * @param mixed $against
     *
     * @return Result
     */
    public function checkIdenticalTo($value, $against)
    {
        return new Result($value === $against, '{name} must be identical to {0}', [$against]);
    }

    /**
     * Check that $value is equals $against.
     *
     * @Check(["is", "equals"])
     *
     * @param mixed $value
     * @param mixed $against
     *
     * @return Result
     */
    public function checkEquals($value, $against)
    {
        return new Result($value == $against, '{name} must equal {0}', [$against]);
    }

    /**
     * Check that $value is truthy.
     *
     * @Check(["isTruthy", "truthy"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkIsTruthy($value)
    {
        return new Result(! ! $value, '{name} must be truthy');
    }

    /**
     * Check that $value is falsy.
     *
     * @Check(["isFalsy", "falsy"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkIsFalsy($value)
    {
        return new Result(! $value, '{name} must be falsy');
    }

    /**
     * Check that $value is identical to true.
     *
     * @Check(["isTrue", "true"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkIsTrue($value)
    {
        return $this->checkIdenticalTo($value, true);
    }

    /**
     * Check that $value is identical to false.
     *
     * @Check(["isTrue", "true"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkIsFalse($value)
    {
        return $this->checkIdenticalTo($value, false);
    }

    /**
     * Check that $value is identical to false.
     *
     * @Check(["hasType", "isType"])
     *
     * @param mixed $value
     * @param string $type
     *
     * @return Result
     */
    public function checkHasType($value, $type)
    {
        return new Result(
            strtolower(gettype($value)) === strtolower($type),
            '{name} must have the type {0}',
            [$type]
        );
    }

    /**
     * Check that $value is scalar.
     *
     * @Check(["isScalar", "scalar"])
     *
     * @param mixed $value
     * @return Result
     */
    public function checkScalar($value)
    {
        return new Result(
            is_scalar($value),
            '{name} must be a scalar'
        );
    }

    /**
     * Check that $value is a boolean.
     *
     * @Check(["isBool", "isBoolean"])
     *
     * @param mixed $value
     * @return Result
     */
    public function checkBool($value)
    {
        return $this->checkHasType($value, 'boolean');
    }

    /**
     * Check that $value is an array.
     *
     * @Check("isArray")
     *
     * @param mixed $value
     * @return Result
     */
    public function checkArray($value)
    {
        return $this->checkHasType($value, 'array');
    }

    /**
     * Check that $value is a float.
     *
     * @Check(["isFloat", "isDouble"])
     *
     * @param mixed $value
     * @return Result
     */
    public function checkFloat($value)
    {
        return $this->checkHasType($value, 'double');
    }

    /**
     * Check that $value is a float.
     *
     * @Check(["isInt", "isInteger"])
     *
     * @param mixed $value
     * @return Result
     */
    public function checkInteger($value)
    {
        return $this->checkHasType($value, 'integer');
    }

    /**
     * Check that $value is a float.
     *
     * @Check("isString")
     *
     * @param mixed $value
     * @return Result
     */
    public function checkString($value)
    {
        return $this->checkHasType($value, 'string');
    }

    /**
     * Check that $value is a float.
     *
     * @Check("isObject")
     *
     * @param mixed $value
     * @return Result
     */
    public function checkObject($value)
    {
        return $this->checkHasType($value, 'object');
    }

    /**
     * Check that $value is null.
     *
     * @Check("isNull")
     *
     * @param mixed $value
     * @return Result
     */
    public function checkNull($value)
    {
        return $this->checkHasType($value, 'NULL');
    }

    /**
     * Check that $value is null.
     *
     * @Check(["isNotNull", "notNull"])
     *
     * @param mixed $value
     * @return Result
     */
    public function checkNotNull($value)
    {
        return new Result(
            ! is_null($value),
            '{name} must not be null'
        );
    }

    /**
     * Check that $value is a resource.
     *
     * @Check("isResource")
     *
     * @param mixed $value
     * @return Result
     */
    public function checkResource($value)
    {
        return $this->checkHasType($value, 'resource');
    }

    /**
     * Check that $value is a resource of the given type.
     *
     * @Check(["isResourceOfType", "resourceType"])
     *
     * @param mixed $value
     * @param string $type
     * @return Result
     */
    public function checkResourceType($value, $type)
    {
        $partialSuccess = is_resource($value);

        $success = $partialSuccess && (strcasecmp(get_resource_type($value), $type) === 0);

        return new Result(
            $success,
            '{name} must be a resource of type {0}',
            [$type]
        );
    }

    /**
     * Check that $value is callable.
     *
     * @Check("isCallable")
     *
     * @param mixed $value
     * @return Result
     */
    public function checkCallable($value)
    {
        return new Result(
            is_callable($callable),
            '{name} must be callable'
        );
    }
}
