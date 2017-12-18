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

class FileSystemCheckProvider implements CheckProvider
{
    use Traits\CanString,
        Traits\SizeConversion,
        Traits\ProvideViaReflection;

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
        $success = $this->canString($value)
            && is_file((string) $value);

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
        $success = $this->canString($value)
            && is_dir((string) $value);

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
        $success = $this->canString($value)
            && is_writable((string) $value);

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
        $success = $this->canString($value)
            && is_readable((string) $value);

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
        $success = $this->canString($value)
            && is_executable((string) $value);

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
        $success = $this->canString($value)
            && is_link((string) $value);

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
        if (!$this->canString($size)) {
            throw new InvalidArgumentException('Second argument must be an integer, a string, or a stringable object');
        }

        $success = $this->canString($value)
            && is_file($value)
            && filesize($value) > $this->sizeToBytes($size);

        return new Result($success, '{name} must be a file that is larger than {0:raw}', [$size]);
    }
}
