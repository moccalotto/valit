<?php

namespace Kahlan\Spec\Suite;

use Valit\Util\File;
use Valit\Util\FileInfo;
use Valit\Result\AssertionResult;
use Valit\Providers\FileSystemCheckProvider;

describe('FileSystemCheckProvider', function () {
    describe('basics', function () {
        it('exists', function () {
            expect(class_exists(FileSystemCheckProvider::class))->toBe(true);
        });

        $subject = new FileSystemCheckProvider();

        it('provides an array of checks', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
        });
    });
});
