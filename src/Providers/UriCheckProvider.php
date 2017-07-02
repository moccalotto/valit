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

class UriCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check if $value is a valid host name.
     *
     * @Check(["isHostname", "hostname"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkHostname($value)
    {
        $success = is_string($value)
            && preg_match('#(?:[\pL\pN\pS-\.])+(?:\.?([\pL\pN]|xn\-\-[\pL\pN-]+)+\.?)#ui', $value);

        return new Result($success, '{name} must be a valid host name');
    }

    /**
     * Check if $value is a valid ipv4 or ipv6 address
     *
     * @Check(["isIpAdrress", "ipAddress"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkIpAddress($value)
    {
        $log_level = error_reporting(0);
        $success = @inet_pton($value) !== false;
        error_reporting($log_level);

        return new Result($success, '{name} must be a valid host name');
    }

    /**
     * Check if $value is a complete and absolute web url
     *
     * @Check(["url", "isUrl"])
     *
     * @param mixed $value
     * @param string|strings[] $scemes
     *
     * @return Result
     */
    public function checkUrl($value, $schemes = ['https', 'http'])
    {
        $schemes = (array) $schemes;

        if (empty($schemes) || (array_filter($schemes, 'is_string') !== $schemes)) {
            throw new InvalidArgumentException('Second argument must be a non-empty array of strings');
        }

        $message = '{name} must be a valid absolute web url';

        if (!is_string($value)) {
            return new Result(false, $message, ['candidate is not a a string']);
        }

        $parts = parse_url($value);

        if (!in_array($parts['scheme'] ?? null, $schemes)) {
            return new Result(false, $message);
        }

        if (empty($parts['host'])) {
            return new Result(false, $message);
        }

        if ($this->checkHostname($value)->success()) {
            return new Result(true, $message);
        }

        if ($this->checkIpAddress($value)->success()) {
            return new Result(true, $message);
        }

        return new Result(false, $message);
    }
}
