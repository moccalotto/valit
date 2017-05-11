<?php

/**
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
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
     * @param mixed $value
     *
     * @return Result
     */
    protected function parseUuid($value, &$version = null, &$variant = null)
    {
        if (!is_string($value)) {
            return false;
        }

        if ($value === '00000000-0000-0000-0000-000000000000') {
            return true;
        }

        $isMatch = preg_match(
            '/[0-9a-f]{8}-[0-9a-f]{4}-([1-5])[0-9a-f]{3}-([89ab])[0-9a-f]{3}-[0-9a-f]{12}$/Ai',
            $value,
            $matches
        );

        $version = $isMatch ? $matches[1] : null;
        $variant = $isMatch ? $matches[2] : null;

        return $isMatch;
    }

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
        return new Result($this->parseUuid($value), '{name} must be a valid UUID');
    }

    /**
     * Check if $value is a uuid.
     *
     * @Check(["isUuidVersion", "uuidVersion"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkUuidVersion($value, $version)
    {
        $version = (int) $version;

        $match = $this->parseUuid($value, $parsedVersion);

        if ($version < 1 || $version > 5) {
            throw new InvalidArgumentException('$version must be an integer in the range [1..5]');
        }

        return new Result($match && $parsedVersion == $version, '{name} must be a version {0} UUID', [$version]);
    }
}
