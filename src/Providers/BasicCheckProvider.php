<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use Valit\Contracts\CheckProvider;
use Valit\Result\SingleAssertionResult;
use Valit\Traits\ProvideViaReflection;
use UnexpectedValueException;

class BasicCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check that $value === $against.
     *
     * @Check(["isIdenticalTo", "identicalTo", "sameAs", "isSameAs"])
     *
     * @param mixed $value
     * @param mixed $against
     *
     * @return SingleAssertionResult
     */
    public function checkIdenticalTo($value, $against)
    {
        return new SingleAssertionResult($value === $against, '{name} must be identical to {0}', [$against]);
    }

    /**
     * Check that $value == $against (loose comparison).
     *
     * @Check(["is", "equals"])
     *
     * @param mixed $value
     * @param mixed $against
     *
     * @return SingleAssertionResult
     */
    public function checkEquals($value, $against)
    {
        return new SingleAssertionResult($value == $against, '{name} must equal {0}', [$against]);
    }

    /**
     * Check that $value is equal to (==) one of the values in $against.
     *
     * @Check(["isOneOf", "oneOf"])
     *
     * @param mixed $value
     * @param array $against
     *
     * @return SingleAssertionResult
     */
    public function checkIsOneOf($value, $against)
    {
        if (!is_array($against)) {
            throw new UnexpectedValueException('$against must be an array');
        }

        $msg = sprintf('{name} must match one of %s', implode(', ', array_map(function ($int) {
            return '{' . $int . '}';
        }, range(0, count($against) - 1))));

        foreach ($against as $match) {
            if ($value == $match) {
                return new SingleAssertionResult(true, $msg, $against);
            }
        }

        return new SingleAssertionResult(false, $msg, $against);
    }

    /**
     * Check that $value is truthy.
     *
     * @Check(["isTruthy", "truthy"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkIsTruthy($value)
    {
        return new SingleAssertionResult((bool) $value, '{name} must be truthy');
    }

    /**
     * Check that $value is falsy.
     *
     * @Check(["isFalsy", "falsy"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkIsFalsy($value)
    {
        return new SingleAssertionResult(!$value, '{name} must be falsy');
    }

    /**
     * Check that $value is identical to true.
     *
     * @Check(["isTrue", "true"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkIsTrue($value)
    {
        return $this->checkIdenticalTo($value, true);
    }

    /**
     * Check that $value is identical to false.
     *
     * @Check(["isFalse", "false"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkIsFalse($value)
    {
        return $this->checkIdenticalTo($value, false);
    }

    /**
     * Check that $value is identical to false.
     *
     * @Check(["hasType", "isType", "typeof"])
     *
     * @param mixed  $value
     * @param string $type
     *
     * @return SingleAssertionResult
     */
    public function checkHasType($value, $type)
    {
        return new SingleAssertionResult(
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
     *
     * @return SingleAssertionResult
     */
    public function checkScalar($value)
    {
        return new SingleAssertionResult(
            is_scalar($value),
            '{name} must be a scalar'
        );
    }

    /**
     * Check that $value is a boolean.
     *
     * @Check(["isBool", "isBoolean", "bool", "boolean"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkBool($value)
    {
        return $this->checkHasType($value, 'boolean');
    }

    /**
     * Check that $value is an array.
     *
     * @Check(["isArray", "array"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkArray($value)
    {
        return $this->checkHasType($value, 'array');
    }

    /**
     * Check that $value is a float.
     *
     * @Check(["isFloat", "isDouble", "float", "double"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkFloat($value)
    {
        return $this->checkHasType($value, 'double');
    }

    /**
     * Check that $value is a float.
     *
     * @Check(["isInt", "isInteger", "int", "integer"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkInteger($value)
    {
        return $this->checkHasType($value, 'integer');
    }

    /**
     * Check that $value is a float.
     *
     * @Check(["isString", "string"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkString($value)
    {
        return $this->checkHasType($value, 'string');
    }

    /**
     * Check that $value is a float.
     *
     * @Check(["isObject", "object"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkObject($value)
    {
        return $this->checkHasType($value, 'object');
    }

    /**
     * Check that $value is null.
     *
     * @Check(["isNull", "null"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
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
     *
     * @return SingleAssertionResult
     */
    public function checkNotNull($value)
    {
        return new SingleAssertionResult(
            !is_null($value),
            '{name} must not be null'
        );
    }

    /**
     * Check that $value is a resource.
     *
     * @Check(["isResource", "resource"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkResource($value)
    {
        return $this->checkHasType($value, 'resource');
    }

    /**
     * Check that $value is a resource of the given type.
     *
     * @Check(["isResourceOfType", "resourceType", "hasResourceType"])
     *
     * @param mixed  $value
     * @param string $type
     *
     * @return SingleAssertionResult
     */
    public function checkResourceType($value, $type)
    {
        $partialSuccess = is_resource($value);

        $success = $partialSuccess && (strcasecmp(get_resource_type($value), $type) === 0);

        return new SingleAssertionResult(
            $success,
            '{name} must be a resource of type {0}',
            [$type]
        );
    }

    /**
     * Check that $value is callable.
     *
     * @Check(["isCallable", "callable"])
     *
     * @param mixed $value
     *
     * @return SingleAssertionResult
     */
    public function checkCallable($value)
    {
        return new SingleAssertionResult(
            is_callable($value),
            '{name} must be callable'
        );
    }
}
