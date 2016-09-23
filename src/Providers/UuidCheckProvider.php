<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace Moccalotto\Valit\Providers;

use InvalidArgumentException;
use Moccalotto\Valit\Result;
use Moccalotto\Valit\Contracts\CheckProvider;
use Moccalotto\Valit\Traits\ProvideViaReflection;

class UuidCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check if $value is a uuid.
     *
     * @Check(["isUuid", "uuid"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkIsUuid($value)
    {
        $success = is_string($value) && (
            $value === '00000000-0000-0000-0000-000000000000'
            || preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value)
        );

        return new Result($success, '{name} must be a valid UUID');
    }
}
