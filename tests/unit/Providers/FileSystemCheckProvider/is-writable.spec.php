<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\File;
use Valit\Util\FileInfo;
use Valit\Result\AssertionResult;
use Valit\Providers\FileSystemCheckProvider;

describe('FileSystemCheckProvider', function () {
    describe('checkIsWritable', function () {
        $subject = new FileSystemCheckProvider();

        File::mock(FileInfo::custom('readonlyDir', ['exists' => true, 'isDir' => true, 'isWritable' => false]));
        File::mock(FileInfo::custom('writableDir', ['exists' => true, 'isDir' => true, 'isWritable' => true]));
        File::mock(FileInfo::custom('readonlyFile', ['exists' => true, 'isFile' => true, 'isWritable' => false]));
        File::mock(FileInfo::custom('writableFile', ['exists' => true, 'isFile' => true, 'isWritable' => true]));
        File::mock(FileInfo::custom('missingFile', ['exists' => false]));

        it('has the correct aliases', function () use ($subject) {
            expect($subject->provides())->toContainKey('isWritable');
            expect($subject->provides())->toContainKey('writable');
        });

        it('returns an AssertionResult', function () use ($subject) {
            $result = $subject->checkIsWritable('');
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('is valid if path is a writable file', function () use ($subject) {
            $result = $subject->checkIsWritable('writableFile');
            expect($result->success())->toBe(true);
        });

        it('is valid if path is a writable dir', function () use ($subject) {
            $result = $subject->checkIsWritable('writableDir');
            expect($result->success())->toBe(true);
        });

        it('is invalid if path is a non-writable dir', function () use ($subject) {
            $result = $subject->checkIsWritable('readonlyDir');
            expect($result->success())->toBe(false);
        });

        it('is invalid if path is a non-writable file', function () use ($subject) {
            $result = $subject->checkIsWritable('readonlyFile');
            expect($result->success())->toBe(false);
        });

        it('is invalid if the path does not exist', function () use ($subject) {
            $result = $subject->checkIsWritable('missingFile');
            expect($result->success())->toBe(false);
        });

        it('is invalid if the given path is not stringable', function () use ($subject) {
            expect($subject->checkIsWritable([])->success())->toBe(false);
            expect($subject->checkIsWritable(null)->success())->toBe(false);
            expect($subject->checkIsWritable(new \DateTime())->success())->toBe(false);
            expect($subject->checkIsWritable(curl_init())->success())->toBe(false);
        });
    });
});
