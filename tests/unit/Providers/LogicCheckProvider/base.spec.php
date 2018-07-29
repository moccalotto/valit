<?php

namespace Kahlan\Spec\Suite;

use Valit\Providers\LogicCheckProvider;

describe('LogicCheckProvider', function () {
    describe('basics', function () {
        it('exists', function () {
            expect(class_exists(LogicCheckProvider::class))->toBe(true);
        });

        $subject = new LogicCheckProvider();

        it('provides an array of checks', function () use ($subject) {
            expect($subject->provides())->toBeAn('array');
        });
    });
});
