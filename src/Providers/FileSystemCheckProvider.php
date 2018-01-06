<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 */

namespace Valit\Providers;

use Valit\Util\Val;
use Valit\Util\Size;
use Valit\Util\Date;
use Valit\Util\File;
use InvalidArgumentException;
use Valit\Contracts\CheckProvider;
use Valit\Traits\ProvideViaReflection;
use Valit\Result\AssertionResult as Result;

class FileSystemCheckProvider implements CheckProvider
{
    use ProvideViaReflection;

    /**
     * Check if $value is an existing file.
     *
     * @Check(["fileExists", "isFile"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkFileExists($value)
    {
        $success = Val::stringable($value)
            && File::info($value)->isFile;

        return new Result($success, '{name} must be the name of an existing file');
    }

    /**
     * Check if $value is an existing directory.
     *
     * @Check(["dirExists", "directoryExists", "isDir", "isDirectory"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkDirExists($value)
    {
        $success = Val::stringable($value)
            && File::info($value)->isDir;

        return new Result($success, '{name} must be the name of an existing directory');
    }

    /**
     * Check if $value is exists on the filesystem and is writable.
     *
     * @Check(["writable", "isWritable"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkIsWritable($value)
    {
        $success = Val::stringable($value)
            && File::info($value)->isWritable;

        return new Result($success, '{name} must be a writable path');
    }

    /**
     * Check if $value is exists on the filesystem and is readable.
     *
     * @Check(["readable", "isReadable"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkIsReadable($value)
    {
        $success = Val::stringable($value)
            && File::info($value)->isReadable;

        return new Result($success, '{name} must be a readable path');
    }

    /**
     * Check if $value is an executable filesystem path.
     *
     * @Check(["isExecutable", "executable"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkExecutable($value)
    {
        $success = Val::stringable($value)
            && File::info($value)->isExecutable;

        return new Result($success, '{name} must be an executable file path');
    }

    /**
     * Check if $value is a filesystem link.
     *
     * @Check(["isLink", "link"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkLink($value)
    {
        $success = Val::stringable($value)
            && File::info($value)->isLink;

        return new Result($success, '{name} must be a filesystem link');
    }

    /**
     * Check if $value is a filename and that the file is larger than $size.
     *
     * @Check(["fileLargerThan", "isFileLargerThan", "fileSizeGreaterThan"])
     *
     * @param mixed      $value The value that must be a file with a size of at least $size
     * @param string|int $size  The file size. It can be a string such as '1.44MB' or '2GiB', or an integer of bytes
     *
     * @return Result
     */
    public function checkLargerThan($value, $size)
    {
        if (!Val::stringable($size)) {
            throw new InvalidArgumentException('Second argument must be an integer, a string, or a stringable object');
        }

        $bytes = Size::toBytes($size);

        $success = Val::stringable($value)
            && File::exists($value)
            && File::info($value)->size > $bytes;

        return new Result($success, '{name} must be a file that is larger than {0:raw}', [$size]);
    }

    /**
     * Check if $value is a filename and that the file is smaller than $size.
     *
     * @Check(["fileSmallerThan", "isFileSmallerThan", "fileSizeLessThan"])
     *
     * @param mixed      $value The value that must be a file with a size of at least $size
     * @param string|int $size  The file size. It can be a string such as '1.44MB' or '2GiB', or an integer of bytes
     *
     * @return Result
     */
    public function checkSmallerThan($value, $size)
    {
        if (!Val::stringable($size)) {
            throw new InvalidArgumentException('Second argument must be an integer, a string, or a stringable object');
        }

        $bytes = Size::toBytes($size);

        $success = Val::stringable($value)
            && File::exists($value)
            && File::info($value)->size < $bytes;

        return new Result($success, '{name} must be a file that is smaller than {0:raw}', [$size]);
    }

    /**
     * Check if $value is a file, that the timestamp denoted by $timeFunc compares
     * to $against given the $compareFunc.
     *
     * Examples:
     * ```php
     * // Example 1
     * Check::that($file)->isFileWhereTime(
     *     'created',
     *     'at',
     *     '1987-01-01 00:00:00'
     * );
     *
     * // Example 2
     * Check::that($file)->fileWhereTime(
     *     'modified',
     *     'beforeOrAt',
     *     '1987-01-01 00:00:00'
     * );
     *
     * // Example 3
     * Check::that($file)->fileWhereTime(
     *     'accessed',
     *     'after',
     *     '5 minutes ago'
     * );
     * ```
     * ---
     *
     * @Check(["fileWhereTime", "isFileWhereTime"])
     *
     * @param mixed  $value       The value that must be a file with a size of at least $size
     * @param string $timeFunc    Which time type should be check. One of
     *                            'created', 'modified', or 'accessed'
     * @param string $compareFunc one of 'before', 'after', 'at', 'beforeOrAt', 'afterOrAt'
     * @param mixed  $date        A DateTimeInterface, a number (i.e. unix timestamp) or a
     *                            string that can be parsed into a DateTime
     *
     * @return Result
     */
    public function checkFileTime($value, $timeFunc, $compareFunc, $date)
    {
        $success = Val::stringable($value)
            && File::exists($value)
            && Date::comparison(
                $compareFunc,
                File::time($value, $timeFunc),
                Date::parse($date)
            );

        return new Result(
            $success,
            '{name} must be a file that has been {0:raw} after {1:raw}',
            [$timeFunc, Date::parse($date)->format('Y-m-d H:i:s')]
        );
    }

    /**
     * Check if $value is a file that was created after $date.
     *
     * @Check(["fileNewerThan", "isFileNewerThan", "fileCreatedAfter", "isFileCreatedAfter"])
     *
     * @param mixed $value The candidate file
     * @param mixed $date  A parseable date
     *
     * @return Result
     */
    public function checkCreatedAfter($value, $date)
    {
        return $this->checkFileTime($value, 'created', 'after', $date);
    }

    /**
     * Check if $value is a file that was created before $date.
     *
     * @Check(["fileOlderThan", "isFileOlderThan", "fileCreatedBefore", "isFileCreatedBefore"])
     *
     * @param mixed $value The candidate file
     * @param mixed $date  A parseable date
     *
     * @return Result
     */
    public function checkCreatedBefore($value, $date)
    {
        return $this->checkFileTime($value, 'created', 'before', $date);
    }

    /**
     * Check if $value is a file that was modified after $date.
     *
     * @Check(["fileModifiedAfter", "isFileModifiedAfter"])
     *
     * @param mixed $value The candidate file
     * @param mixed $date  A parseable date
     *
     * @return Result
     */
    public function checkModifiedAfter($value, $date)
    {
        return $this->checkFileTime($value, 'modified', 'after', $date);
    }

    /**
     * Check if $value is a file that was modified before $date.
     *
     * @Check(["fileModifiedBefore", "isFileModifiedBefore"])
     *
     * @param mixed $value The candidate file
     * @param mixed $date  A parseable date
     *
     * @return Result
     */
    public function checkModifiedBefore($value, $date)
    {
        return $this->checkFileTime($value, 'modified', 'before', $date);
    }

    /**
     * Check if $value is a file that was accessed after $date.
     *
     * @Check(["fileAccessedAfter", "isFileAccessedAfter"])
     *
     * @param mixed $value The candidate file
     * @param mixed $date  A parseable date
     *
     * @return Result
     */
    public function checkAccessedAfter($value, $date)
    {
        return $this->checkFileTime($value, 'accessed', 'after', $date);
    }

    /**
     * Check if $value is a file that was accessed before $date.
     *
     * @Check(["fileAccessedBefore", "isFileAccessedBefore"])
     *
     * @param mixed $value The candidate file
     * @param mixed $date  A parseable date
     *
     * @return Result
     */
    public function checkAccessedBefore($value, $date)
    {
        return $this->checkFileTime($value, 'accessed', 'before', $date);
    }
}
