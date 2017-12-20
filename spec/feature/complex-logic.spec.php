<?php

namespace Kahlan\Spec\Suite;

use DateTime;
use Exception;
use Valit\Check;
use Valit\Ensure;
use Valit\Result\AssertionResult;
use Valit\Validators\ValueValidator;
use Valit\Exceptions\InvalidValueException;

function logicFeatureTest($allowUnauthenticatedAccess, $request)
{
    Ensure::that($request)->passesAll([
        Check::oneOf([  // the request must either be an array or an object
            Check::value()->isArray(),
            Check::value()->isObject()
        ]),

        // Check::allOrNone([
        //     'headers/last-modified-at' => Check::value()->isParsableDate(),
        //     'headers/last-modified-at' => Check::value()->isDateInThePast(),
        //     'headers/last-modified-at' => Check::value()->isDateAfter(new DateTime('15 days ago')),
        // ]),

        // We must either allow unauthenticated access
        // or we must have some kind of authentication token
        Check::anyOf([
            Check::that($allowUnauthenticatedAccess)->isTrue(),
            Check::oneOf([
                'headers/x-auth-token'    => 'isHexString & hasLength(42)',
                'body/authToken'          => Check::value()->isHexString()->hasLength(42),
            ]),
        ]),

        Check::that(true)->isTrue(),    // Add some complexity
    ]);
};


describe('Large Logic', function () {
    it('throws when executed without authentication and allowUnauthenticatedAccess = false', function () {
        expect(function () {
            $allowUnauthenticatedAccess = false;
            (object) $request = [];
            logicFeatureTest($allowUnauthenticatedAccess, $request);
        })->toThrow();
    });
    it('throws when auth header is not correctly formatted', function () {
        expect(function () {
            $allowUnauthenticatedAccess = false;
            $request = [
                'headers' => [
                    'x-auth-token' => 'foo',
                ],
            ];
            logicFeatureTest($allowUnauthenticatedAccess, $request);
        })->toThrow();
    });

    it('throws when both authToken and x-auth-token are present and allowUnauthenticatedAccess = false', function () {
        expect(function () {
            $allowUnauthenticatedAccess = false;
            $request = [
                'headers' => [
                    'x-auth-token' => '795973753bbb41b6adf3523589911b5721ee2ba2c5',
                ],
                'body' => [
                    'authToken' => '795973753bbb41b6adf3523589911b5721ee2ba2c5',
                ],
            ];
            logicFeatureTest($allowUnauthenticatedAccess, $request);
        })->toThrow();
    });

    it('is valid when executed with x-auth-token in headers', function () {
        expect(function () {
            $allowUnauthenticatedAccess = false;
            $request = [
                'headers' => ['x-auth-token' => '795973753bbb41b6adf3523589911b5721ee2ba2c5'],
            ];
            logicFeatureTest($allowUnauthenticatedAccess, $request);
        })->not->toThrow();
    });

    it('is valid when executed with authToken in body', function () {
        expect(function () {
            $allowUnauthenticatedAccess = false;
            $request = [
                'body' => ['authToken' => '795973753bbb41b6adf3523589911b5721ee2ba2c5'],
            ];
            logicFeatureTest($allowUnauthenticatedAccess, $request);
        })->not->toThrow();
    });

    it('is valid when executed witout authentication and allowUnauthenticatedAccess = true', function () {
        expect(function () {
            $allowUnauthenticatedAccess = true;
            $request = [];
            logicFeatureTest($allowUnauthenticatedAccess, $request);
        })->not->toThrow();
    });
    it('is valid when executed with two authentication methods and allowUnauthenticatedAccess = true', function () {
        expect(function () {
            $allowUnauthenticatedAccess = true;
            $request = [
                'headers' => [
                    'x-auth-token' => '795973753bbb41b6adf3523589911b5721ee2ba2c5',
                ],
                'body' => [
                    'authToken' => '795973753bbb41b6adf3523589911b5721ee2ba2c5',
                ],
            ];
            logicFeatureTest($allowUnauthenticatedAccess, $request);
        })->not->toThrow();
    });

    // it('throws when last-modified-at header is present, but incorrectly formatted', function () {
    //     expect(function () {
    //         $allowUnauthenticatedAccess = true;
    //         $request = [
    //             'headers' => [
    //                 'last-modified-at' => 'sovs',
    //             ],
    //         ];
    //         logicFeatureTest($allowUnauthenticatedAccess, $request);
    //     })->toThrow();
    // });
    // it('throws when last-modified-at header is in the future', function () {
    //     expect(function () {
    //         $allowUnauthenticatedAccess = true;
    //         $request = [
    //             'headers' => [
    //                 'last-modified-at' => gmdate('D, d M Y H:i:s \G\M\T', time() + 666),
    //             ],
    //         ];
    //         logicFeatureTest($allowUnauthenticatedAccess, $request);
    //     })->toThrow();
    // });

    // it('is valid when last-modified-at header is within the last 15 days', function () {
    //     expect(function () {
    //         $allowUnauthenticatedAccess = true;
    //         $request = [
    //             'headers' => [
    //                 'last-modified-at' => gmdate('D, d M Y H:i:s \G\M\T', time() - 24 * 60 * 60),
    //             ],
    //         ];
    //         logicFeatureTest($allowUnauthenticatedAccess, $request);
    //     })->not->toThrow();
    // });
});
