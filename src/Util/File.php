<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

namespace Valit\Util;

use RuntimeException;
use InvalidArgumentException;

/**
 * Provide functionality to access files and file properties.
 */
abstract class File
{
    /**
     * Internal.
     *
     * @var FileInfo[]
     */
    public static $mocks = [];

    /**
     * Override the file info for a single file.
     *
     * @param FileInfo $file
     */
    public static function mock(FileInfo $file)
    {
        static::$mocks[$file->name] = $file;
    }

    /**
     * Remove mock of a given file.
     *
     * @param string $filename
     */
    public static function removeOverride($filename)
    {
        unset(static::$mocks[$filename]);
    }

    /**
     * Get the file info.
     *
     * @param string $file The file name
     *
     * @return FileInfo
     *
     * @throws InvalidArgumentException if $file is not a string or a stringable object
     */
    public static function info($file)
    {
        $file = Val::toString($file, '$file must be a string or a stringable object');

        if (isset(static::$mocks[$file])) {
            return static::$mocks[$file];
        }

        return new FileInfo($file);
    }

    /**
     * Get the file time.
     *
     * @param string $file     path to file
     * @param string $timeFunc one of 'created', 'modified', 'accessed'
     *
     * @return \DateTimeInterface|null
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
