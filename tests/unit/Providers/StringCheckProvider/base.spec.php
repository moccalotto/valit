<?php

namespace Kahlan\Spec\Suite;

use Valit\Providers\StringCheckProvider;

describe('StringCheckProvider', function () {
    describe('basics', function () {
        it('exists', function () {
            expect(class_exists(StringCheckProvider::class))->toBe(true);
        });

        $subject = new StringCheckProvider();

        it('provides an array of checks', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
        });
    });
});
