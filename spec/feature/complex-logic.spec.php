<?php

namespace Kahlan\Spec\Suite;

use DateTime;
use Exception;
use Valit\Value;
use Valit\Check;
use Valit\Ensure;
use Valit\Result\AssertionResult;
use Valit\Validators\ValueValidator;
use Valit\Exceptions\InvalidValueException;

function logicFeatureTest($allowUnauthenticatedAccess, $request)
{
    Ensure::that($request)->passesAll([
        // if headers are present, they must be an array
        'headers' => Check::allOrNone([
            'present',
            'isArray',
        ]),

        // if body is present, it must be an array
        'body' => Check::allOrNone([
            'present',
            'isArray',
        ]),

        // We do allow any proxy-forward headers
        Check::notAnyOf([
            'headers/forwarded'         => 'present',
            'headers/x-forwarded-for'   => 'present',
            'headers/x-forwarded-host'  => 'present',
            'headers/x-forwarded-proto' => 'present',
        ]),

        // if we have a last-modified-at header,
        // it must be parseable and it must be a
        // date within the last 15 days.
        'headers/last-modified-at' => Check::allOrNone([
            'present',
            Value::isString(),
            Value::isDateInThePast(),
            // Value::isDateString('D, d M Y H:i:s e'), // this assertion does not work on hhvm for some reason
            Value::isDateAfter(new DateTime('15 days ago')),
        ]),

        // We must either allow unauthenticated access
        // or we must have some kind of authentication token
        Check::anyOf([
            Check::that($allowUnauthenticatedAccess)->isTrue(),
            Check::oneOf([
                'headers/x-auth-token'    => 'isHexString & hasLength(42)',
                'body/authToken'          => Value::isHexString()->hasLength(42),
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

    it('throws when last-modified-at header is not a date', function () {
        expect(function () {
            $allowUnauthenticatedAccess = true;
            $request = [
                'headers' => [
                    'last-modified-at' => 'foo',
            // gmdate('D, d M Y H:i:s \G\M\T', strtotime('1 day ago')),
                ],
            ];
            logicFeatureTest($allowUnauthenticatedAccess, $request);
        })->toThrow();
    });

    it('throws when last-modified-at header is a date more than 15 days ago', function () {
        expect(function () {
            $allowUnauthenticatedAccess = true;
            $request = [
                'headers' => [
                    'last-modified-at'  => gmdate('D, d M Y H:i:s \G\M\T', strtotime('116 days ago')),
                ],
            ];
            logicFeatureTest($allowUnauthenticatedAccess, $request);
        })->toThrow();
    });

    it('is valid when last-modified-header is yesterday', function () {
        expect(function () {
            $allowUnauthenticatedAccess = true;
            $request = [
                'headers' => [
                    'last-modified-at'  => gmdate('D, d M Y H:i:s \G\M\T', strtotime('1 day ago')),
                ],
            ];
            logicFeatureTest($allowUnauthenticatedAccess, $request);
        })->not->toThrow();
    });

    it('throws when body is not an array', function () {
        expect(function () {
            $allowUnauthenticatedAccess = true;
            $request = [
                'body' => 'not an array',
            ];
            logicFeatureTest($allowUnauthenticatedAccess, $request);
        })->toThrow();
    });

    it('throws when headers is not an array', function () {
        expect(function () {
            $allowUnauthenticatedAccess = true;
            $request = [
                'headers' => 'not an array',
            ];
            logicFeatureTest($allowUnauthenticatedAccess, $request);
        })->toThrow();
    });
});
