<?php

/**
 * This file is part of the Valit package.
 *
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 *
 * @codingStandardsIgnoreFile
 */

namespace spec\Valit\Providers;

use ArrayObject;
use PhpSpec\ObjectBehavior;

class FileSystemCheckProviderSpec extends ObjectBehavior
{
    function _makeFile()
    {
        $dir = sys_get_temp_dir();
        return tempnam($dir, 'FileSystemCheckProviderSpec');
    }

    function letGo()
    {
        $globStr = sprintf('%s/FileSystemCheckProviderSpec*', sys_get_temp_dir());

        foreach (glob($globStr) as $file) {
            unlink($file);
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
        $file = $this->_makeFile();

        $this->provides()->shouldHaveKey('fileExists');
        $this->provides()->shouldHaveKey('isFile');

        $this->checkFileExists($file)->shouldHaveType('Valit\Result\AssertionResult');
        $this->checkFileExists($file)->success()->shouldBe(true);
        $this->checkFileExists(new \SimpleXMLElement("<x>$file</x>"))->success()->shouldBe(true);

        $this->checkFileExists('/foo/bar/baz')->success()->shouldBe(false);
        $this->checkFileExists('')->success()->shouldBe(false);
        $this->checkFileExists([])->success()->shouldBe(false);
        $this->checkFileExists(null)->success()->shouldBe(false);
        $this->checkFileExists(1234567890)->success()->shouldBe(false);
        $this->checkFileExists((object) [])->success()->shouldBe(false);
        $this->checkFileExists(curl_init())->success()->shouldBe(false);
    }
}
