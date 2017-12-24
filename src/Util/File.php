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
     * @var FileInfo[]
     *
     * @internal
     */
    public static $overrides = [];

    /**
     * Override the file info for a single file.
     *
     * @param FileInfo $file
     */
    public static function override(FileInfo $file)
    {
        static::$overrides[$file->name] = $file;
    }

    /**
     * Get the file info.
     *
     * @param string $file The file name
     *
     * @return array|false Array containing stat info if the file exists or false if it does not
     *
     * @see {http://php.net/manual/en/function.stat.php} for more info about the result array
     *
     * @throws InvalidArgumentException if $file is not a string or a stringable object
     */
    public static function info($file)
    {
        $file = Val::toString($file, '$file must be a string or a stringable object');

        if (isset(static::$overrides[$file])) {
            return static::$overrides[$file];
        }

        return new FileInfo($file);
    }

    /**
     * Get the file time.
     *
     * @param string $file     path to file
     * @param string $timeFunc one of 'created', 'modified', 'accessed'
     *
     * @return int
     *
     * @throws RuntimeException         if file could not be found
     * @throws InvalidArgumentException if $timeFunc was an incorrect value
     * @throws InvalidArgumentException if $file cannot be coerced into a string
     */
    public static function time($file, $timeFunc = 'created')
    {
        $info = static::info($file);

        if (!$info->exists) {
            throw new RuntimeException('File does not exist');
        }

        if ($timeFunc === 'created') {
            return $info->createdAt;
        }

        if ($timeFunc === 'modified') {
            return $info->modifiedAt;
        }

        if ($timeFunc === 'accessed') {
            return $info->accessedAt;
        }

        throw new InvalidArgumentException('File time function must be one of "created", "accessed" or "modified"');
    }

    /**
     * Does $file exist?
     *
     * @param string $file
     *
     * @return bool
     */
    public static function exists($file)
    {
        return static::info($file)->exists;
    }
}
