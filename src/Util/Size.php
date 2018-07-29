<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit\Util;

/**
 * Utility Class for parsing suffixed size strings to bytes.
 */
abstract class Size
{
    /**
     * Convert a human-readable size-string to bytes.
     *
     * For instance:
     *      100 is "converted" to 100
     *      '100kB' is converted to 100.000
     *      '100KiB' is converted to 102.400
     *      '100GB' is converted to 100,000,000,000
     *      '100GiB' is converted to 107.374.182.400
     *
     * @param string|int $size
     *
     * @return int
     */
    public static function toBytes($size)
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

        $size = (string) $size;

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
