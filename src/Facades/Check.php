<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace Moccalotto\Valit\Facades;

use Moccalotto\Valit\Fluent;
use Moccalotto\Valit\Manager;
use Moccalotto\Valit\ContainerValidator;

class Check
{
    public static function that($value)
    {
        return new Fluent(Manager::instance(), $value, false);
    }

    public static function container($value)
    {
        return new ContainerValidator(Manager::instance(), $value, false);
    }
}
