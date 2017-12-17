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
use Valit\Contracts\CheckProvider;
use Valit\Result\AssertionResult as Result;

class FileSystemCheckProvider implements CheckProvider
{
    use Traits\CanString,
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
     * Check if $value is a readable file.
     *
     * @Check(["readableFile", "isReadableFile"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkFileReadable($value)
    {
        $success = $this->canString($value)
            && is_file((string) $value)
            && is_readable((string) $value);

        return new Result($success, '{name} must be the name of an readable file');
    }

    /**
     * Check if $value is a readable directory.
     *
     * @Check(["readableDir", "isReadableDir", "readableDirectory", "isReadableDirectory"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkDirReadable($value)
    {
        $success = $this->canString($value)
            && is_dir((string) $value)
            && is_readable((string) $value);

        return new Result($success, '{name} must be the name of a readable directory');
    }

    /**
     * Check if $value is a writable file.
     *
     * @Check(["writableFile", "isWritableFile"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkFileWritable($value)
    {
        $success = $this->canString($value)
            && is_file((string) $value)
            && is_writable((string) $value);

        return new Result($success, '{name} must be the name of a writable file');
    }

    /**
     * Check if $value is a writable directory.
     *
     * @Check(["isWritableDir", "writableDir"])
     *
     * @param mixed $value
     *
     * @return Result
     */
    public function checkDirWritable($value)
    {
        $success = $this->canString($value)
            && is_dir((string) $value)
            && is_writable((string) $value);

        return new Result($success, '{name} must be the name of writable directory');
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
}
