<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Moccalotto\Valit\Facades;

use Moccalotto\Valit\Fluent;
use Moccalotto\Valit\Manager;

class Ensure
{
    public static function that($value)
    {
        return new Fluent(Manager::instance(), $value, true);
    }

    public static function passes($value)
    {
        return new ContainerValidator(Manager::instance(), $value, true);
    }
}
