<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

namespace Moccalotto\Valit\Providers;

use Moccalotto\Valit\Result;
use Moccalotto\Valit\Traits\ProvideViaReflection;

class FileSystemCheckProvider
{
    use ProvideViaReflection;

    public function checkFileExists($value)
    {
        return new Result(is_file($value), '{name} must be the complete name of an existing file');
    }

    public function checkDirExists($value)
    {
        return new Result(is_dir($value), '{name} must be the complete name of an existing file');
    }

    public function checkFileReadable($value)
    {
        return new Result(
            is_file($value) && is_readable($value),
            '{name} must be the complete name of an existing file'
        );
    }

    public function checkDirReadable($value)
    {
        return new Result(
            is_dir($value) && is_readable($value),
            '{name} must be the complete name of an existing file'
        );
    }

    /**
     * @Check(['isWriteableFile', 'writeableFile', 'isWritableFile', 'writableFile'])
     */
    public function checkFileWriteable($value)
    {
        return new Result(
            is_file($value) && is_writable($value)
        );
    }

    public function checkDirWritable($value)
    {
        return new Result(
            is_dir($value) && is_writable($value)
        );
    }

    public function checkExecutable($value)
    {
        return new Result(
            is_executable($value),
            '{name} must be executable for you'
        );
    }
}
