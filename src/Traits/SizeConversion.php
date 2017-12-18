<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Traits;

trait SizeConversion
{
    public function sizeToBytes($size)
    {
        $sizeMap = [
            ['kB', 1000, 1],
            ['MB', 1000, 2],
            ['GB', 1000, 3],
            ['TB', 1000, 4],
            ['PB', 1000, 5],
            ['EB', 1000, 6],
            ['ZB', 1000, 7],
            ['YB', 1000, 8],

            ['KiB', 1024, 1],
            ['MiB', 1024, 2],
            ['GiB', 1024, 3],
            ['TiB', 1024, 4],
            ['PiB', 1024, 5],
            ['EiB', 1024, 6],
            ['ZiB', 1024, 7],
            ['YiB', 1024, 8],
        ];

        $getSize = function ($suffix) use ($size) {
            $suffixLength = strlen($suffix);
            if (substr($size, -$suffixLength) === $suffix) {
                return trim(substr($size, 0, -$suffixLength));
            }

            return false;
        };

        foreach ($sizeMap as list($suffix, $base, $power)) {
            $sizeNumber = $getSize($suffix);

            if ($sizeNumber === false) {
                continue;
            }

            $multiplier = bcpow($base, $power, 0);

            return (int) bcmul($multiplier, $sizeNumber);
        }

        return (int) $size; // the size has no known suffix, we assume it is bytes.
    }
}
