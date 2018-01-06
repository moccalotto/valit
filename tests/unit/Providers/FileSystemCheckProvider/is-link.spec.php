<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\File;
use Valit\Util\FileInfo;
use Valit\Result\AssertionResult;
use Valit\Providers\FileSystemCheckProvider;

describe('FileSystemCheckProvider', function () {
    describe('checkLink', function () {
        $subject = new FileSystemCheckProvider();

        File::mock(FileInfo::custom('missing', ['exists' => false]));
        File::mock(FileInfo::custom('linkToDir', ['exists' => true, 'isDir' => true, 'isLink' => true]));
        File::mock(FileInfo::custom('linkToFile', ['exists' => true, 'isFile' => true, 'isLink' => true]));
        File::mock(FileInfo::custom('realDir', ['exists' => true, 'isDir' => true, 'isLink' => false]));
        File::mock(FileInfo::custom('realFile', ['exists' => true, 'isFile' => true, 'isLink' => false]));

        it('has the correct aliases', function () use ($subject) {
            expect($subject->provides())->toContainKey('isLink');
            expect($subject->provides())->toContainKey('link');
        });

        it('returns an AssertionResult', function () use ($subject) {
            $result = $subject->checkLink('');
            expect($result)->toBeAnInstanceOf(AssertionResult::class);
        });

        it('is valid if path is an link file', function () use ($subject) {
            $result = $subject->checkLink('linkToFile');
            expect($result->success())->toBe(true);
        });

        it('is valid if path is an link dir', function () use ($subject) {
            $result = $subject->checkLink('linkToDir');
            expect($result->success())->toBe(true);
        });

        it('is invalid if path is a non-link dir', function () use ($subject) {
            $result = $subject->checkLink('realDir');
            expect($result->success())->toBe(false);
        });

        it('is invalid if path is a non-link file', function () use ($subject) {
            $result = $subject->checkLink('realFile');
            expect($result->success())->toBe(false);
        });

        it('is invalid if the path does not exist', function () use ($subject) {
            $result = $subject->checkLink('missing');
            expect($result->success())->toBe(false);
        });

        it('is invalid if the given path is not stringable', function () use ($subject) {
            expect($subject->checkLink([])->success())->toBe(false);
            expect($subject->checkLink(null)->success())->toBe(false);
            expect($subject->checkLink(new \DateTime())->success())->toBe(false);
            expect($subject->checkLink(curl_init())->success())->toBe(false);
        });
    });
});
