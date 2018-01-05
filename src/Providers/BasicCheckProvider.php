<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
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
     * Check that $value is equal to (==) one of the values in $allowedValues.
     *
     * If you give multiple arguments to this function, each argument will
     * be treated as a possible option.
     * If you give only one argument to this function, then that argument must
     * be iterable (array or Traversable). The entries in that variable will
     * be treated as the options allowed.
     *
     * Code examples:
     *
     * ```php
     * // Check that $foo is either 'foo' or 'bar'
     * Check::that($foo)->isOneOf('foo', 'bar');
     * Check::that($foo)->isOneOf(['foo', 'bar']);
     *
     * // Check that $foo is either ['a', 'b'] or ['c', 'd']
     * Check::that($foo)->isOneOf(['a', 'b'], ['c', 'd']);
     * Check::that($foo)->isOneOf([ ['a', 'b'], ['c', 'd'] ]);
     * ```
     *
     * ---
     *
     * @Check(["isOneOf", "oneOf"])
     *
     * @param mixed            $value
     * @param mixed[]|iterable $allowedValues,... The allowed values
     *
     * @return AssertionResult
     */
    public function checkIsOneOf($value, $allowedValues)
    {
        // If $allowedValues is variadic instead of array
        if (func_num_args() > 2) {
            $allowedValues = array_slice(func_get_args(), 1);
        }

        Val::mustBe($allowedValues, 'iterable');

        $msg = '{name} must be one of {0:imploded}';

        foreach ($allowedValues as $match) {
            if ($value == $match) {
                return new AssertionResult(true, $msg, [$allowedValues]);
            }
        }

        return new AssertionResult(false, $msg, [$allowedValues]);
    }

    /**
     * Check that $value is NOT equal to (==) any of the values in $unacceptableValues.
     *
     * See isOneOf() for examples.
     *
     * @Check(["isNotOneOf", "notOneOf"])
     *
     * @param mixed            $value
     * @param mixed[]|iterable $unacceptableValues
     *
     * @return AssertionResult
     */
    public function checkIsNotOneOf($value, $unacceptableValues)
    {
        // If $unacceptableValues is variadic instead of array
        if (func_num_args() > 2) {
            $unacceptableValues = array_slice(func_get_args(), 1);
        }

        Val::mustBe($unacceptableValues, 'iterable');

        $msg = '{name} must be one of {0:imploded}';

        foreach ($unacceptableValues as $match) {
            if ($value == $match) {
                return new AssertionResult(false, $msg, [$unacceptableValues]);
            }
        }

        return new AssertionResult(true, $msg, [$unacceptableValues]);
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
     * | $type          | Validation                                    |
     * |:-------------- |:-------------------------                     |
     * | `null`         | `is_null()`                                   |
     * | `object`       | `is_object()`                                 |
     * | `int`          | `is_int()`                                    |
     * | `integer`      | `is_int()`                                    |
     * | `bool`         | `is_bool()`                                   |
     * | `boolean`      | `is_bool()`                                   |
     * | `string`       | `is_string()`                                 |
     * | `float`        | `is_float()`                                  |
     * | `double`       | `is_float()`                                  |
     * | `numeric`      | `is_numeric()`                                |
     * | `nan`          | `is_nan()`                                    |
     * | `inf`          | `is_inf()`                                    |
     * | `callable`     | `is_callable()`                               |
     * | `iterable`     | `is_array() || is_a($value, 'Traversable')`   |
     * | `countable`    | `is_array() || is_a($value, 'Cointable')`     |
     * | `arrayable`    | `is_array() || is_a($value, 'ArrayAccess')`   |
     * | `container`    | `iterable`, `countable` and `arrayable`       |
     * | `stringable`   | scalar or object with a`__toString()` method  |
     * | _class name_   | `is_a()`                                      |
     * | _foo[]_        | array of _foo_                                |
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
     * // check that $foo is an array of floats
     * // or an array of integers
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
        Val::mustBe($type, 'string|string[]');

        $types = Val::explodeAndTrim('|', $type);

        return new AssertionResult(
            Val::is($value, $types),
            '{name} must have the {0:raw} {1:imploded}',
            [
                count($types) < 2 ? 'type' : 'types',
                $types,
            ]
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
     * Check that $value is a an integer.
     *
     * If $comparison and $against are given, we also check that $value
     * comparest to $against via the $comparison.
     *
     * For instance:
     *
     * ```php
     * // Check that foo is an integer.
     * Check::that($foo)->isInt();
     *
     * // Check that foo is an integer that is greater than 20
     * Check::that($foo)->isInt('>', 20);
     *
     * // Check that foo is an integer that is greater than or equal to 0
     * Check::that($foo)->isInt('>=', 20);
     * Check::that($foo)->isInt('≥', 20);   // alternate syntax
     *
     * // Check that foo is an integer that is equal to 5
     * Check::that($foo)->isInt('=', 5);
     * ```
     *
     * @Check(["isInt", "isInteger", "int", "integer"])
     *
     * @param mixed       $value
     * @param string|null $comparison one of [null, '<', '>', '=', '>=', '≥', '<=', '≤']
     * @param int         $against
     *
     * @return AssertionResult
     */
    public function checkInteger($value, $comparison = null, $against = null)
    {
        Val::mustBe($comparison, 'null|string');

        if ($comparison === '>=') {
            $comparison = '≥';
        }
        if ($comparison === '<=') {
            $comparison = '≤';
        }

        if ($comparison === null) {
            Val::mustBe($against, 'null');

            return $this->checkHasType($value, 'integer');
        }

        Val::mustBe($against, 'int');

        $message = '{name} must be an integer that is {0:raw} {1:int}';

        if (!is_int($value)) {
            return new AssertionResult(false, $message, [$comparison, $against]);
        }
        if ($comparison === '>') {
            return new AssertionResult($value > $against, $message, [$comparison, $against]);
        }
        if ($comparison === '<') {
            return new AssertionResult($value < $against, $message, [$comparison, $against]);
        }
        if ($comparison === '≥') {
            return new AssertionResult($value >= $against, $message, [$comparison, $against]);
        }
        if ($comparison === '≤') {
            return new AssertionResult($value >= $against, $message, [$comparison, $against]);
        }
        if ($comparison === '=') {
            return new AssertionResult($value == $against, $message, [$comparison, $against]);
        }

        throw new InvalidArgumentException('Second arhument must be NULL or one of [>, <, =, >=, ≥, <=, ≤]');
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
