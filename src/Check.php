<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit;

use Valit\Validators\ContainerValidator;
use Valit\Validators\ValueValidator;
use Valit\Logic\OneOf;

class Check
{
    /**
     * Check that a single variable passes certain criteria.
     *
     * @param mixed $value the value to check
     *
     * @return ValueValidator A instance that has been configured to not
     *                              throw any exceptions. You must inspect the ValueValidator
     *                              object (for instance via the valid() method) to
     *                              find out if all checks passed
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
     * @param Array|Traversable $scenarios
     *
     * @return OneOf
     */
    public static function oneOf($scenarios)
    {
        return new OneOf(
            Manager::instance(),
            $scenarios
        );
    }
}
