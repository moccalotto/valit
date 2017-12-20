<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit;

use Valit\Assertion\Template;
use Valit\Validators\ValueValidator;
use Valit\Validators\ContainerValidator;

abstract class Check
{
    /**
     * Check that a single variable passes certain criteria.
     *
     * @param mixed $value the value to check
     *
     * @return ValueValidator A instance that has been configured to not
     *                        throw any exceptions. You must inspect the ValueValidator
     *                        object (for instance via the valid() method) to
     *                        find out if all checks passed
     */
    public static function that($value)
    {
        return new ValueValidator(Manager::instance(), $value, false);
    }

    /**
     * Ensure that a container passes certain criteria.
     *
     * @param mixed $container the container to check
     *
     * @return ContainerValidator a ContainerValidator object that has been
     *                            configured to throw a InvalidValueException as soon as a
     *                            single validation fails
     */
    public static function container($container)
    {
        return new ContainerValidator(Manager::instance(), $container, false);
    }

    /**
     * Create a check template.
     *
     * @return Template
     */
    public static function value()
    {
        return new Template();
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
     * @return Logic\AllOfOrNone
     */
    public static function allOrNone($scenarios)
    {
        return new Logic\AllOfOrNone(
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
     * Short hand to creating templates.
     *
     * @return Template
     */
    public static function __callStatic($methodName, $args)
    {
        return call_user_func_array(
            [static::value(), $methodName],
            $args
        );
    }
}
