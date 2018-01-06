<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\File;
use Valit\Util\FileInfo;
use Valit\Result\AssertionResult;
use Valit\Providers\FileSystemCheckProvider;

describe('FileSystemCheckProvider', function () {
    describe('checkIsReadable', function () {
        $subject = new FileSystemCheckProvider();

        File::mock(FileInfo::custom('missing', ['exists' => false]));
        File::mock(FileInfo::custom('readableDir', ['exists' => true, 'isDir' => true, 'isReadable' => true]));
        File::mock(FileInfo::custom('readableFile', ['exists' => true, 'isFile' => true, 'isReadable' => true]));
        File::mock(FileInfo::custom('unreadableDir', ['exists' => true, 'isDir' => true, 'isReadable' => false]));
        File::mock(FileInfo::custom('unreadableFile', ['exists' => true, 'isFile' => true, 'isReadable' => false]));

        it('has the correct aliases', function () use ($subject) {
            expect($subject->provides())->toContainKey('isReadable');
            expect($subject->provides())->toContainKey('readable');
        });

        it('returns an AssertionResult', function () use ($subject) {
            $result = $subject->checkIsReadable('');
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('is valid if path is a readable file', function () use ($subject) {
            $result = $subject->checkIsReadable('readableFile');
            expect($result->success())->toBe(true);
        });

        it('is valid if path is a readable dir', function () use ($subject) {
            $result = $subject->checkIsReadable('readableDir');
            expect($result->success())->toBe(true);
        });

        it('is invalid if path is a non-readable dir', function () use ($subject) {
            $result = $subject->checkIsReadable('unreadableDir');
            expect($result->success())->toBe(false);
        });

        it('is invalid if path is a non-readable file', function () use ($subject) {
            $result = $subject->checkIsReadable('unreadableFile');
            expect($result->success())->toBe(false);
        });

        it('is invalid if the path does not exist', function () use ($subject) {
            $result = $subject->checkIsReadable('missing');
            expect($result->success())->toBe(false);
        });

        it('is invalid if the given path is not stringable', function () use ($subject) {
            expect($subject->checkIsReadable([])->success())->toBe(false);
            expect($subject->checkIsReadable(null)->success())->toBe(false);
            expect($subject->checkIsReadable(new \DateTime())->success())->toBe(false);
            expect($subject->checkIsReadable(curl_init())->success())->toBe(false);
        });
    });
});
