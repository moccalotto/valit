<?php

namespace Moccalotto\Valit\Facades;

use Moccalotto\Valit\Fluent;
use Moccalotto\Valit\Manager;

class Check
{
    public static function that($value)
    {
        return new Fluent(Manager::instance(), $value, false);
    }
}
