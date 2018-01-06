<?php

namespace Kahlan\Spec\Suite;

use Valit\Providers\BasicCheckProvider;

describe('BasicCheckProvider', function () {
    describe('basics', function () {
        it('exists', function () {
            expect(class_exists(BasicCheckProvider::class))->toBe(true);
        });

        $subject = new BasicCheckProvider();

        it('provides an array of checks', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
        });
    });
});
