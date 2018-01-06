<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\File;
use Valit\Util\FileInfo;
use Valit\Result\AssertionResult;
use Valit\Providers\FileSystemCheckProvider;

describe('FileSystemCheckProvider', function () {
    describe('checkDirExists', function () {
        $subject = new FileSystemCheckProvider();

        File::mock(FileInfo::custom('existingDir', ['exists' => true, 'isDir' => true]));
        File::mock(FileInfo::custom('existingFile', ['exists' => true, 'isFile' => true]));
        File::mock(FileInfo::custom('missingFile', ['exists' => false]));

        it('has the correct aliases', function () use ($subject) {
            expect($subject->provides())->toContainKey('dirExists');
            expect($subject->provides())->toContainKey('directoryExists');
            expect($subject->provides())->toContainKey('isDir');
            expect($subject->provides())->toContainKey('isDirectory');
        });

        it('returns an AssertionResult', function () use ($subject) {
            $result = $subject->checkDirExists('existingFile');
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('is valid if path is an existing dir', function () use ($subject) {
            $result = $subject->checkDirExists('existingDir');
            expect($result->success())->toBe(true);
        });

        it('is invalid if path is not a dir', function () use ($subject) {
            $result = $subject->checkDirExists('existingFile');
            expect($result->success())->toBe(false);
        });

        it('is invalid if the path does not exist', function () use ($subject) {
            $result = $subject->checkDirExists('missingFile');
            expect($result->success())->toBe(false);
        });

        it('is invalid if the given path is not stringable', function () use ($subject) {
            expect($subject->checkDirExists([])->success())->toBe(false);
            expect($subject->checkDirExists(null)->success())->toBe(false);
            expect($subject->checkDirExists(new \DateTime())->success())->toBe(false);
            expect($subject->checkDirExists(curl_init())->success())->toBe(false);
        });
    });
});
