<?php

namespace Kahlan\Spec\Suite;

use Valit\Providers\ArrayCheckProvider;

describe('ArrayCheckProvider', function () {
    describe('basics', function () {
        it('exists', function () {
            expect(class_exists(ArrayCheckProvider::class))->toBe(true);
        });

        $subject = new ArrayCheckProvider();

        it('provides an array of checks', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
        });
    });
});
