<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit;

use Valit\Validators\ValueValidator;

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
