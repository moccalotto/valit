<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Providers;

use Valit\Traits;
use InvalidArgumentException;
use Valit\Contracts\CheckProvider;
use Valit\Result\AssertionResult as Result;

class UriCheckProvider implements CheckProvider
{
    use Traits\CanString,
        Traits\ProvideViaReflection;

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
     * Check if $value is a valid ipv4 or ipv6 address.
     *
     * @Check(["isIpAdrress", "ipAddress"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkIpAddress($value)
    {
        $stringable = is_string($value)
            || is_object($value) && is_callable($value, '__toString');

        $log_level = error_reporting(0);
        $success = $stringable && @inet_pton((string) $value) !== false;
        error_reporting($log_level);

        return new Result($success, '{name} must be a valid ip address');
    }

    /**
     * Check if $value is a complete and absolute web url.
     *
     * @Check(["url", "isUrl"])
     *
     * @param mixed           $value
     * @param string|string[] $schemes
     *
     * @return Result
     */
    public function checkUrl($value, $schemes = ['https', 'http'])
    {
        $schemes = array_filter(
            array_filter((array) $schemes, 'is_string'),
            'strlen'
        );

        if (empty($schemes)) {
            throw new InvalidArgumentException('Second argument must be a non-empty array of strings');
        }

        $message = '{name} must be a valid absolute web url';

        if (!is_string($value)) {
            return new Result(false, $message, ['candidate is not a a string']);
        }

        $parts = parse_url($value);

        if (!in_array((isset($parts['scheme']) ? $parts['scheme'] : null), $schemes)) {
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
