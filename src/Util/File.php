<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Util;

use InvalidArgumentException;

/**
 * Provide functionality to access files and file properties.
 */
abstract class File
{
    /**
     * Get the file time.
     *
     * @param string $file     path to file
     * @param string $timeFunc one of 'created', 'modified', 'accessed'
     *
     * @return int
     */
    public static function time($file, $timeFunc = 'created')
    {
        if (!file_exists($file)) {
            throw new RuntimeException(sprintf(
                'File »%s« does not exist',
                $file
            ));
        }

        if ($timeFunc === 'created') {
            return filectime($file);
        }

        if ($timeFunc === 'modified') {
            return filemtime($file);
        }

        if ($timeFunc === 'accessed') {
            return fileatime($file);
        }

        throw new InvalidArgumentException('File time function must be one of "created", "accessed" or "modified"');
    }
}
