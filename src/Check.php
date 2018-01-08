<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit;

use Valit\Assertion\AssertionBag;
use Valit\Validators\ValueValidator;

/**
 * Facade for checking values and containers without throwing exceptions.
 */
abstract class Check
{
    /**
     * Check that a single variable passes certain criteria.
     *
     * @param mixed $value the value to check
     *
     * @return ValueValidator A instance that has been configured to not
     *                        throw any exceptions. You must inspect the ValueValidator
     *                        object (for instance via the success() method) to
     *                        find out if all checks passed
     */
    public static function that($value)
    {
        return new ValueValidator(Manager::instance(), $value, false);
    }

    /**
     * Create an AssertionBag.
     *
     * @return AssertionBag
     */
    public static function value()
    {
        return new AssertionBag([]);
    }

    /**
     * Check that exactly one of the given scenarios
     * succeed.
     *
     * @param array|\Traversable $scenarios
     *
     * @return Logic\OneOf
     */
    public static function oneOf($scenarios)
    {
        return new Logic\OneOf(
            Manager::instance(),
            $scenarios
        );
    }

    /**
     * Check that exactly all of the given scenarios succeed.
     *
     * @param array|\Traversable $scenarios
     *
     * @return Logic\AllOf
     */
    public static function allOf($scenarios)
    {
        return new Logic\AllOf(
            Manager::instance(),
            $scenarios
        );
    }

    /**
     * Check that one of more of the given scenarios succeed.
     *
     * @param array|\Traversable $scenarios
     *
     * @return Logic\AnyOf
     */
    public static function anyOf($scenarios)
    {
        return new Logic\AnyOf(
            Manager::instance(),
            $scenarios
        );
    }

    /**
     * Check that none of of the given scenarios succeed.
     *
     * @param array|\Traversable $scenarios
     *
     * @return Logic\NoneOf
     */
    public static function noneOf($scenarios)
    {
        return new Logic\NoneOf(
            Manager::instance(),
            $scenarios
        );
    }

    /**
     * Check that all or none of of the given scenarios succeed.
     *
     * @param array|\Traversable $scenarios
     *
     * @return Logic\AllOrNone
     */
    public static function allOrNone($scenarios)
    {
        return new Logic\AllOrNone(
            Manager::instance(),
            $scenarios
        );
    }

    /**
     * Alias of noneOf().
     *
     * Check that none of of the given scenarios succeed.
     *
     * @param array|\Traversable $scenarios
     *
     * @return Logic\NoneOf
     */
    public static function notAnyOf($scenarios)
    {
        return static::noneOf($scenarios);
    }

    /**
     * Check that the given scenario does not succeed.
     *
     * @param mixed $scenario
     *
     * @return Logic\Not
     */
    public static function not($scenario)
    {
        return new Logic\Not(
            Manager::instance(),
            $scenario
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
     * @param mixed $else
     *
     * @return Logic\Conditional
     */
    public static function ifThen($condition, $then, $else = true)
    {
        return new Logic\Conditional(
            Manager::instance(),
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
     * otherwise the $else condition must evaluate to true.
     *
     * @param mixed $condition
     * @param mixed $then
     * @param mixed $else
     *
     * @return Logic\Conditional
     */
    public static function ifThenElse($condition, $then, $else)
    {
        return static::ifThen($condition, $then, $else);
    }

    /**
     * Short hand to creating an AssertionBag.
     *
     * @param string  $methodName
     * @param mixed[] $args
     *
     * @return AssertionBag
     */
    public static function __callStatic($methodName, $args)
    {
        return call_user_func_array(
            [static::value(), $methodName],
            $args
        );
    }
}
