<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\File;
use Valit\Util\FileInfo;
use Valit\Result\AssertionResult;
use Valit\Providers\FileSystemCheckProvider;

describe('FileSystemCheckProvider', function () {
    describe('checkExecutable', function () {
        $subject = new FileSystemCheckProvider();

        File::mock(FileInfo::custom('missing', ['exists' => false]));
        File::mock(FileInfo::custom('executableDir', ['exists' => true, 'isDir' => true, 'isExecutable' => true]));
        File::mock(FileInfo::custom('executableFile', ['exists' => true, 'isFile' => true, 'isExecutable' => true]));
        File::mock(FileInfo::custom('unexecutableDir', ['exists' => true, 'isDir' => true, 'isExecutable' => false]));
        File::mock(FileInfo::custom('unexecutableFile', ['exists' => true, 'isFile' => true, 'isExecutable' => false]));

        it('has the correct aliases', function () use ($subject) {
            expect($subject->provides())->toContainKey('isExecutable');
            expect($subject->provides())->toContainKey('executable');
        });

        it('returns an AssertionResult', function () use ($subject) {
            $result = $subject->checkExecutable('');
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('is valid if path is an executable file', function () use ($subject) {
            $result = $subject->checkExecutable('executableFile');
            expect($result->success())->toBe(true);
        });

        it('is valid if path is an executable dir', function () use ($subject) {
            $result = $subject->checkExecutable('executableDir');
            expect($result->success())->toBe(true);
        });

        it('is invalid if path is a non-executable dir', function () use ($subject) {
            $result = $subject->checkExecutable('unexecutableDir');
            expect($result->success())->toBe(false);
        });

        it('is invalid if path is a non-executable file', function () use ($subject) {
            $result = $subject->checkExecutable('unexecutableFile');
            expect($result->success())->toBe(false);
        });

        it('is invalid if the path does not exist', function () use ($subject) {
            $result = $subject->checkExecutable('missing');
            expect($result->success())->toBe(false);
        });

        it('is invalid if the given path is not stringable', function () use ($subject) {
            expect($subject->checkExecutable([])->success())->toBe(false);
            expect($subject->checkExecutable(null)->success())->toBe(false);
            expect($subject->checkExecutable(new \DateTime())->success())->toBe(false);
            expect($subject->checkExecutable(curl_init())->success())->toBe(false);
        });
    });
});
    /*
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
     */
