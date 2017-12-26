<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use InvalidArgumentException;
use Valit\Result\AssertionResult as Result;
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;

class UuidCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * @param mixed       $value
     * @param string|null $version (out parameter)
     * @param string|null $variant
     *
     * @return bool
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
     * @param int   $version
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

        return new Result($match && $parsedVersion == $version, '{name} must be a version {0:int} UUID', [$version]);
    }

    /**
     * Check if $value is a version 1 uuid.
     *
     * @Check(["uuidV1", "isUuidV1"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkUidV1($value)
    {
        return $this->checkUuidVersion($value, 1);
    }

    /**
     * Check if $value is a version 2 uuid.
     *
     * @Check(["uuidV2", "isUuidV2"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkUidV2($value)
    {
        return $this->checkUuidVersion($value, 2);
    }

    /**
     * Check if $value is a version 3 uuid.
     *
     * @Check(["uuidV3", "isUuidV3"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkUidV3($value)
    {
        return $this->checkUuidVersion($value, 3);
    }

    /**
     * Check if $value is a version 4 uuid.
     *
     * @Check(["uuidV4", "isUuidV4"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkUidV4($value)
    {
        return $this->checkUuidVersion($value, 4);
    }

    /**
     * Check if $value is a version 5 uuid.
     *
     * @Check(["uuidV5", "isUuidV5"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkUidV5($value)
    {
        return $this->checkUuidVersion($value, 5);
    }
}
