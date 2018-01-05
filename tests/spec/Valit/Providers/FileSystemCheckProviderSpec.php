<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2018
 * @license MIT
 *
 * @codingStandardsIgnoreFile
 */

namespace spec\Valit\Providers;

use ArrayObject;
use PhpSpec\ObjectBehavior;

class FileSystemCheckProviderSpec extends ObjectBehavior
{
    function _withFile($chmod, $callback = null)
    {
        if (is_callable($chmod)) {
            $callback = $chmod;
            $chmod = 0666;
        }

        $file = $this->_makeFile();

        chmod($file, $chmod);
        $callback($file, $chmod);

        chmod($file, 0666);
        unlink($file);
    }

    function _makeFile()
    {
        $dir = sys_get_temp_dir();
        return tempnam($dir, 'FileSystemCheckProviderSpec');
    }

    function _makeDir()
    {
        $dirname = sys_get_temp_dir() . '/FileSystemCheckProviderSpec' . uniqid();
        mkdir($dirname);

        return $dirname;
    }

    function _withDir($chmod, $callback = null)
    {
        if (is_callable($chmod)) {
            $callback = $chmod;
            $chmod = 0666;
        }

        $dir = $this->_makeDir();
        chmod($dir, $chmod);

        $callback($dir, $chmod);

        chmod($dir, 0666);

        rmdir($dir);
    }

    function letGo()
    {
        $globStr = sprintf('%s/FileSystemCheckProviderSpec*', sys_get_temp_dir());

        foreach (glob($globStr) as $file) {
            chmod($file, 0600);
            if (is_file($file)) {
                unlink($file);
            }
            if (is_dir($file)) {
                rmdir($file);
            }
        }
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Valit\Providers\FileSystemCheckProvider');
    }

    function it_provides_checks()
    {
        $this->provides()->shouldBeArray();
    }

    function it_checks_FileExists()
    {
        $this->provides()->shouldHaveKey('fileExists');
        $this->provides()->shouldHaveKey('isFile');

        $this->_withFile(function ($file) {
            $this->checkFileExists($file)->shouldHaveType('Valit\Result\AssertionResult');
            $this->checkFileExists($file)->success()->shouldBe(true);
            $this->checkFileExists(new \SimpleXMLElement("<x>$file</x>"))->success()->shouldBe(true);
        });

        $this->_withDir(function ($dir) {
            $this->checkFileExists($dir)->success()->shouldBe(false);
        });

        $this->checkFileExists('/foo/bar/baz')->success()->shouldBe(false);
        $this->checkFileExists('')->success()->shouldBe(false);
        $this->checkFileExists([])->success()->shouldBe(false);
        $this->checkFileExists(null)->success()->shouldBe(false);
        $this->checkFileExists(1234567890)->success()->shouldBe(false);
        $this->checkFileExists((object) [])->success()->shouldBe(false);
        $this->checkFileExists(curl_init())->success()->shouldBe(false);
    }

    function it_checks_DirExists()
    {
        $this->provides()->shouldHaveKey('directoryExists');
        $this->provides()->shouldHaveKey('isDirectory');
        $this->provides()->shouldHaveKey('dirExists');
        $this->provides()->shouldHaveKey('isDir');

        $this->_withDir(function ($dir) {
            $this->checkDirExists($dir)->shouldHaveType('Valit\Result\AssertionResult');
            $this->checkDirExists($dir)->success()->shouldBe(true);
            $this->checkDirExists(new \SimpleXMLElement("<x>$dir</x>"))->success()->shouldBe(true);
        });

        $this->_withFile(function ($file) {
            $this->checkDirExists($file)->success()->shouldBe(false);
        });

        $this->checkDirExists('/foo/bar/baz')->success()->shouldBe(false);
        $this->checkDirExists('')->success()->shouldBe(false);
        $this->checkDirExists([])->success()->shouldBe(false);
        $this->checkDirExists(null)->success()->shouldBe(false);
        $this->checkDirExists(1234567890)->success()->shouldBe(false);
        $this->checkDirExists((object) [])->success()->shouldBe(false);
        $this->checkDirExists(curl_init())->success()->shouldBe(false);
    }

    function it_checks_IsWritable()
    {
        $this->provides()->shouldHaveKey('isWritable');
        $this->provides()->shouldHaveKey('writable');

        $this->_withFile(0200, function ($file) {
            $this->checkIsWritable($file)->shouldHaveType('Valit\Result\AssertionResult');
            $this->checkIsWritable($file)->success()->shouldBe(true);
            $this->checkIsWritable(new \SimpleXMLElement("<x>$file</x>"))->success()->shouldBe(true);
        });
        $this->_withDir(0200, function ($dir) {
            $this->checkIsWritable($dir)->success()->shouldBe(true);
        });

        $this->_withFile(0400, function ($file) {
            $this->checkIsWritable($file)->success()->shouldBe(false);
        });
        $this->_withDir(0400, function ($dir) {
            $this->checkIsWritable($dir)->success()->shouldBe(false);
        });

        $this->checkIsWritable('/foo/bar/baz')->success()->shouldBe(false);
        $this->checkIsWritable('')->success()->shouldBe(false);
        $this->checkIsWritable([])->success()->shouldBe(false);
        $this->checkIsWritable(null)->success()->shouldBe(false);
        $this->checkIsWritable(1234567890)->success()->shouldBe(false);
        $this->checkIsWritable((object) [])->success()->shouldBe(false);
        $this->checkIsWritable(curl_init())->success()->shouldBe(false);
    }

    function it_checks_IsReadable()
    {
        $this->provides()->shouldHaveKey('isReadable');
        $this->provides()->shouldHaveKey('readable');

        $this->_withFile(0400, function ($file) {
            $this->checkIsReadable($file)->shouldHaveType('Valit\Result\AssertionResult');
            $this->checkIsReadable($file)->success()->shouldBe(true);
            $this->checkIsReadable(new \SimpleXMLElement("<x>$file</x>"))->success()->shouldBe(true);
        });
        $this->_withDir(0400, function ($file) {
            $this->checkIsReadable($file)->success()->shouldBe(true);
        });

        $this->_withFile(0200, function ($file) {
            $this->checkIsReadable($file)->success()->shouldBe(false);
        });
        $this->_withDir(0200, function ($file) {
            $this->checkIsReadable($file)->success()->shouldBe(false);
        });

        $this->checkIsReadable('/foo/bar/baz')->success()->shouldBe(false);
        $this->checkIsReadable('')->success()->shouldBe(false);
        $this->checkIsReadable([])->success()->shouldBe(false);
        $this->checkIsReadable(null)->success()->shouldBe(false);
        $this->checkIsReadable(1234567890)->success()->shouldBe(false);
        $this->checkIsReadable((object) [])->success()->shouldBe(false);
        $this->checkIsReadable(curl_init())->success()->shouldBe(false);
    }

    function it_checks_Executable()
    {
        $this->provides()->shouldHaveKey('isExecutable');
        $this->provides()->shouldHaveKey('executable');

        $this->_withFile(0700, function ($file) {
            $this->checkExecutable($file)->shouldHaveType('Valit\Result\AssertionResult');
            $this->checkExecutable($file)->success()->shouldBe(true);
            $this->checkExecutable(new \SimpleXMLElement("<x>$file</x>"))->success()->shouldBe(true);
        });
        $this->_withDir(0700, function ($dir) {
            $this->checkExecutable($dir)->success()->shouldBe(true);
        });

        $this->_withFile(0600, function ($file) {
            $this->checkExecutable($file)->success()->shouldBe(false);
        });
        $this->_withDir(0600, function ($dir) {
            $this->checkExecutable($dir)->success()->shouldBe(false);
        });

        $this->checkExecutable('/foo/bar/baz')->success()->shouldBe(false);
        $this->checkExecutable('')->success()->shouldBe(false);
        $this->checkExecutable([])->success()->shouldBe(false);
        $this->checkExecutable(null)->success()->shouldBe(false);
        $this->checkExecutable(1234567890)->success()->shouldBe(false);
        $this->checkExecutable((object) [])->success()->shouldBe(false);
        $this->checkExecutable(curl_init())->success()->shouldBe(false);
    }

    function it_checks_Link()
    {
        $this->provides()->shouldHaveKey('isLink');
        $this->provides()->shouldHaveKey('link');

        $this->_withDir(0700, function ($dir) {
            $this->checkLink($dir)->shouldHaveType('Valit\Result\AssertionResult');
            $this->checkLink($dir)->success()->shouldBe(false);

            $this->_withFile(0600, function ($file) use ($dir) {
                $link = "$dir/test-link";
                symlink($file, $link);

                $this->checkLink($link)->success()->shouldBe(true);
                $this->checkLink(new \SimpleXMLElement("<r>$link</r>"))->success()->shouldBe(true);

                unlink($link);
            });
        });

        $this->checkLink('/foo/bar/baz')->success()->shouldBe(false);
        $this->checkLink('')->success()->shouldBe(false);
        $this->checkLink([])->success()->shouldBe(false);
        $this->checkLink(null)->success()->shouldBe(false);
        $this->checkLink(1234567890)->success()->shouldBe(false);
        $this->checkLink((object) [])->success()->shouldBe(false);
        $this->checkLink(curl_init())->success()->shouldBe(false);
    }
}
