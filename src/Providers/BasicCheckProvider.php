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

        $msg = sprintf('{name} must match one of %s', implode(', ', array_map(function ($int) {
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
     * Check that $value is identical to false.
     *
     * $type can be a string with a type name such as:
     *  `'int'`, `'float'`, `'bool'`, `'array'` or even `'callable'`
     * or it can be a fully qualified class name such as
     *  `'Valit\Check'`
     *
     * It can also be an array of the above types. If it is an
     * array, then $value can be any of the given values.
     * Example:
     *  `['int', 'DateTimeInterface']`
     *
     * It can also be a string with many types separated by a pipe `|` characer.
     * Example:
     *  `'int|float'`, `'string | DateTimeInterface'`
     *
     *  Code examples:
     *
     *  ```php
     *  // example 1
     *  Check::that($foo)->hasType('callable');
     *
     *  // example 2
     *  Check::that($foo)->hasType('float | int');
     *
     *  // example 3
     *  Check::that($foo)->hasType(['object', 'array'])
     *
     *  // example 4
     *  Check::that($foo)->hasType('DateTime|DateTimeImmutable')
     *  ```
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
