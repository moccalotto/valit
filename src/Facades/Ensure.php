<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Moccalotto\Valit\Facades;

use Moccalotto\Valit\Fluent;
use Moccalotto\Valit\Manager;
use Moccalotto\Valit\ContainerValidator;

class Ensure
{
    /**
     * Ensure that a single variable passes certain criteria.
     *
     * @param mixed $value the value to check
     *
     * @return Fluent a Fluent object that has been configured to throw a
     *                InvalidValueException as soon as a single validation fails
     */
    public static function that($value)
    {
        return new Fluent(Manager::instance(), $value, true);
    }

    /**
     * Ensure that a container passes certain criteria.
     *
     * @param mixed $container the container to check
     *
     * @return ContainerValidator A ContainerValidator object that has been
     *                            configured to NOT throw ValidationExceptions in case
     *                            of failed checks. You can inspect the ContainerValidationResult
     *                            to get information about the failed checks.
     */
    public static function container($container)
    {
        return new ContainerValidator(Manager::instance(), $container, true);
    }
}
