<?php

namespace Kahlan\Spec\Suite;

use SplFileInfo;
use Valit\Util\FileInfo;

describe('FileInfo', function () {
    it('exists', function () {
        expect(class_exists(FileInfo::class))->toBe(true);
    });

    describe('::__construct()', function () {
        it('is initializable with a non-existing file', function () {
            $subject = new FileInfo('foo');
            expect($subject->exists)->toBe(false);
        });

        it('is initializable with an existing file', function () {
            $tmpFile = tempnam('/tmp', 'FileInfo-kahlan-test');
            $subject = new FileInfo($tmpFile);
            expect($subject->exists)->toBe(true);
            unlink($tmpFile);
        });

        it('is initializable with an SplFileInfo of a file that does not exist', function () {
            $info = new SplFileInfo('foo');
            $subject = new FileInfo($info);
            expect($subject->exists)->toBe(false);
        });

        it('is initializable with an SplFileInfo of a file that exists', function () {
            $tmpFile = tempnam('/tmp', 'FileInfo-kahlan-test');
            $info = new SplFileInfo($tmpFile);
            $subject = new FileInfo($info);
            expect($subject->exists)->toBe(true);
            unlink($tmpFile);
        });
    });
});
