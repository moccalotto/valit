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
use Valit\Result\AssertionResult;
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;

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
     * @return AssertionResult
     */
    public function checkIdenticalTo($value, $against)
    {
        return new AssertionResult($value === $against, '{name} must be identical to {0}', [$against]);
    }

    /**
     * Check that $value == $against (loose comparison).
     *
     * Examples:
     *
     * | $value     | $equals       | Valid     |
     * |:-----------|:--------------|:----------|
     * | true       | true          | yes       |
     * | true       | 1             | yes       |
     * | "true"     | 1             | no        |
     * | "true"     | true          | yes       |
     * | "true"     | "foo"         | no        |
     * | "1"        | true          | yes       |
     * | "1"        | 1             | yes       |
     * | "1"        | "foo"         | no        |
     * | "0"        | true          | no        |
     * | "0"        | false         | yes       |
     * | "0"        | 0             | yes       |
     *
     * @Check(["is", "equals"])
     *
     * @param mixed $value
     * @param mixed $equals
     *
     * @return AssertionResult
     */
    public function checkEquals($value, $equals)
    {
        return new AssertionResult($value == $equals, '{name} must equal {0}', [$equals]);
    }

    /**
     * Check that $value is equal to (==) one of the values in $against.
     *
     * @Check(["isOneOf", "oneOf"])
     *
     * @param mixed              $value
     * @param array|\Traversable $possibleValues
     *
     * @return AssertionResult
     */
    public function checkIsOneOf($value, $possibleValues)
    {
        Val::mustBe($possibleValues, 'iterable');

        $msg = sprintf('{name} must be one of %s', implode(', ', array_map(function ($int) {
            return '{'.$int.'}';
        }, range(0, Val::count($possibleValues) - 1))));

        foreach ($possibleValues as $match) {
            if ($value == $match) {
                return new AssertionResult(true, $msg, $possibleValues);
            }
        }

        return new AssertionResult(false, $msg, $possibleValues);
    }

    /**
     * Check that $value is NOT equal to (==) any of the values in $against.
     *
     * @Check(["isNotOneOf", "notOneOf"])
     *
     * @param mixed              $value
     * @param array|\Traversable $unacceptableValues
     *
     * @return AssertionResult
     */
    public function checkIsNotOneOf($value, $unacceptableValues)
    {
        Val::mustBe($unacceptableValues, 'iterable');

        $msg = sprintf('{name} must not be one of %s', implode(', ', array_map(function ($int) {
            return '{'.$int.'}';
        }, range(0, Val::count($unacceptableValues) - 1))));

        foreach ($unacceptableValues as $match) {
            if ($value == $match) {
                return new AssertionResult(false, $msg, $unacceptableValues);
            }
        }

        return new AssertionResult(true, $msg, $unacceptableValues);
    }

    /**
     * Check that $value is truthy.
     *
     * @Check(["isTruthy", "truthy"])
     *
     * @param mixed $value
     *
     * @return AssertionResult
     */
    public function checkIsTruthy($value)
    {
        return new AssertionResult((bool) $value, '{name} must be truthy');
    }

    /**
     * Check that $value is falsy.
     *
     * @Check(["isFalsy", "falsy"])
     *
     * @param mixed $value
     *
     * @return AssertionResult
     */
    public function checkIsFalsy($value)
    {
        return new AssertionResult(!$value, '{name} must be falsy');
    }

    /**
     * Check that $value is identical to true.
     *
     * @Check(["isTrue", "true"])
     *
     * @param mixed $value
     *
     * @return AssertionResult
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
     * @return AssertionResult
     */
    public function checkIsFalse($value)
    {
        return $this->checkIdenticalTo($value, false);
    }

    /**
     * Check that $value has a given type.
     *
     * Possible types:
     *
     * | $type      | Validation                |
     * |:---------- |:------------------------- |
     * | int        | `is_int()`                |
     * | integer    | `is_int()`                |
     * | bool       | `is_bool()`               |
     * | boolean    | `is_bool()`               |
     * | string     | `is_string()`             |
     * | float      | `is_float()`              |
     * | double     | `is_float()`              |
     * | numeric    | `is_numeric()`            |
     * | nan        | `is_nan()`                |
     * | inf        | `is_inf()`                |
     * | callable   | `is_callable()`           |
     * | iterable   | `array`, `Traversable`    |
     * | countable  | `array`, `Cointable`      |
     * | arrayable  | `array`, `ArrayAccess`    |
     * | [fqcn]     | `is_a()`                  |
     *
     * Code examples:
     *
     * ```php
     * // single type
     * Check::that($foo)->hasType('callable');
     *
     * // multiple allowed types via the pipe character
     * Check::that($foo)->hasType('float | int');
     *
     * // Check that $foo is an array of floats
     * // or an array of integers.
     * Check::that($foo)->hasType('float[] | int[]')
     *
     * // mixing classes, interfaces and basic types.
     * Check::that($foo)->hasType('int|DateTime|DateTimeImmutable')
     *
     * // multiple types via array notation
     * Check::that($foo)->hasType(['object', 'array'])
     * ```
     *
     * ---
     *
     * @Check(["hasType", "isType", "typeof"])
     *
     * @param mixed           $value
     * @param string|string[] $type
     *
     * @return AssertionResult
     */
    public function checkHasType($value, $type)
    {
        return new AssertionResult(
            Val::is($value, $type),
            '{name} must have the type(s) {0}',
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
     * @return AssertionResult
     */
    public function checkScalar($value)
    {
        return new AssertionResult(
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
     * @return AssertionResult
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
     * @return AssertionResult
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
     * @return AssertionResult
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
     * @return AssertionResult
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
     * @return AssertionResult
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
     * @return AssertionResult
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
     * @return AssertionResult
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
     * @return AssertionResult
     */
    public function checkNotNull($value)
    {
        return new AssertionResult(
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
     * @return AssertionResult
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
     * @return AssertionResult
     */
    public function checkResourceType($value, $type)
    {
        $partialSuccess = is_resource($value);

        $success = $partialSuccess && (strcasecmp(get_resource_type($value), $type) === 0);

        return new AssertionResult(
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
     * @return AssertionResult
     */
    public function checkCallable($value)
    {
        return new AssertionResult(
            is_callable($value),
            '{name} must be callable'
        );
    }
}
