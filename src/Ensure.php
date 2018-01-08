<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit;

use Valit\Validators\ValueValidator;

/**
 * Facade for checking values and containers, throwing an exception if an assertion fails.
 */
abstract class Ensure
{
    /**
     * Ensure that a single variable passes certain criteria.
     *
     * @param mixed $value the value to check
     *
     * @return ValueValidator instance that has been configured to throw a
     *                        InvalidValueException as soon as a single validation fails
     */
    public static function that($value)
    {
        return new ValueValidator(Manager::instance(), $value, true);
    }

    /**
     * Check that exactly one of the given scenarios succeed.
     *
     * @param array|\Traversable $scenarios
     *
     * @return ValueValidator instance that has been configured to throw a
     *                        InvalidValueException as soon as a single validation fails
     */
    public static function oneOf($scenarios, $value = null)
    {
        $hasValue = func_num_args() > 1;

        return static::that($value)->executeCheck('passesOneOf', [$scenarios, Manager::instance(), $hasValue]);
    }

    /**
     * Check that at least one of the given scenarios succeed.
     *
     * @param array|\Traversable $scenarios
     *
     * @return ValueValidator instance that has been configured to throw a
     *                        InvalidValueException as soon as a single validation fails
     */
    public static function anyOf($scenarios, $value = null)
    {
        $hasValue = func_num_args() > 1;

        return static::that($value)->executeCheck('passesAnyOf', [$scenarios, Manager::instance(), $hasValue]);
    }

    /**
     * Check that all of the given scenarios succeed.
     *
     * @param array|\Traversable $scenarios
     *
     * @return ValueValidator instance that has been configured to throw a
     *                        InvalidValueException as soon as a single validation fails
     */
    public static function allOf($scenarios, $value = null)
    {
        $hasValue = func_num_args() > 1;

        return static::that($value)->executeCheck('passesAllOf', [$scenarios, Manager::instance(), $hasValue]);
    }

    /**
     * Check that none of the given scenarios succeed.
     *
     * @param array|\Traversable $scenarios
     *
     * @return ValueValidator instance that has been configured to throw a
     *                        InvalidValueException as soon as a single validation fails
     */
    public static function noneOf($scenarios, $value = null)
    {
        $hasValue = func_num_args() > 1;

        return static::that($value)->executeCheck('passesNoneOf', [$scenarios, Manager::instance(), $hasValue]);
    }

    /**
     * Alias of noneOf().
     *
     * Check that none of of the given scenarios succeed.
     *
     * @param array|\Traversable $scenarios
     * @param mixed              $value
     *
     * @return Logic\NoneOf
     */
    public static function notAnyOf($scenarios, $value = null)
    {
        return static::noneOf($scenarios, $value);
    }

    /**
     * Check all or none of the givens scenarios succeed.
     *
     * @param array|\Traversable $scenarios
     *
     * @return ValueValidator instance that has been configured to throw a
     *                        InvalidValueException as soon as a single validation fails
     */
    public static function allOrNone($scenarios, $value = null)
    {
        $hasValue = func_num_args() > 1;

        return static::that($value)->executeCheck('passesAllOrNone', [$scenarios, Manager::instance(), $hasValue]);
    }

    /**
     * Check conditional if-then-else clause.
     *
     * if the given $condition evaluates to true,
     * the $then must also evaluate to true,
     *
     * @param mixed $condition
     * @param mixed $then
     * @param mixed $else
     * @param mixed $value
     *
     * @return Logic\Conditional
     */
    public static function ifThenElse($condition, $then, $else = true, $value = null)
    {
        return static::that($value)->passesConditional(
            $condition,
            $then,
            $else
        );
    }

    /**
     * Check conditional if-then clause.
     *
     * if the given $condition evaluates to true,
     * the $then must also evaluate to true,
     *
     * @param mixed $condition
     * @param mixed $then
     * @param mixed $value
     *
     * @return Logic\Conditional
     */
    public static function ifThen($condition, $then, $value = null)
    {
        return static::ifThenElse(
            $condition,
            $then,
            true,
            $value
        );
    }

    /**
     * Check that none of the given scenarios succeed.
     *
     * @param mixed $scenario The scenario that may not succeed
     *
     * @return ValueValidator instance that has been configured to throw a
     *                        InvalidValueException as soon as a single validation fails
     */
    public static function not($scenario, $value = null)
    {
        $hasValue = func_num_args() > 1;

        return static::that($value)->executeCheck('doesNotPass', [$scenario, Manager::instance(), $hasValue]);
    }
}
