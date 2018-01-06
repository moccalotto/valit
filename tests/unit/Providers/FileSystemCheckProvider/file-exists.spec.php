<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\File;
use Valit\Util\FileInfo;
use Valit\Result\AssertionResult;
use Valit\Providers\FileSystemCheckProvider;

describe('FileSystemCheckProvider', function () {
    describe('checkFileExists', function () {
        $subject = new FileSystemCheckProvider();

        File::mock(FileInfo::custom('existingFile', ['exists' => true, 'isFile' => true]));
        File::mock(FileInfo::custom('existingDir', ['exists' => true, 'isDir' => true]));
        File::mock(FileInfo::custom('missingFile', ['exists' => false]));

        it('has the correct aliases', function () use ($subject) {
            expect($subject->provides())->toContainKey('fileExists');
            expect($subject->provides())->toContainKey('isFile');
        });

        it('returns an AssertionResult', function () use ($subject) {
            $result = $subject->checkFileExists('existingFile');
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('is valid if path is an existing file', function () use ($subject) {
            $result = $subject->checkFileExists('existingFile');
            expect($result->success())->toBe(true);
        });

        it('is invalid if path is a dir', function () use ($subject) {
            $result = $subject->checkFileExists('existingDir');
            expect($result->success())->toBe(false);
        });

        it('is invalid if the path does not exist', function () use ($subject) {
            $result = $subject->checkFileExists('missingFile');
            expect($result->success())->toBe(false);
        });

        it('is invalid if the given path is not stringable', function () use ($subject) {
            expect($subject->checkFileExists([])->success())->toBe(false);
            expect($subject->checkFileExists(null)->success())->toBe(false);
            expect($subject->checkFileExists(new \DateTime())->success())->toBe(false);
            expect($subject->checkFileExists(curl_init())->success())->toBe(false);
        });
    });
});
