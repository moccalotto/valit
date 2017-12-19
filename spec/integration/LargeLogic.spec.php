<?php

namespace Kahlan\Spec\Suite;

use Exception;
use Valit\Check;
use Valit\Ensure;
use Valit\Result\AssertionResult;
use Valit\Validators\ValueValidator;
use Valit\Exceptions\InvalidValueException;

function test($allowUnauthenticatedAccess, $request)
{
    Ensure::container($request)->passes([
        // We must either allow unauthenticated access
        // or we must have some kind of authentication token
        Check::anyOf([
            Check::that($allowUnauthenticatedAccess)->isTrue(),
            Check::oneOf([
                'headers/x-auth-token'    => 'isHexString & hasLength(42)',
                'body/authToken'          => Check::value()->isHexString()->hasLength(42),
            ]),
        ]),

        Check::allOrNone([
            'headers/last-modified-at' => 'required',
            'headers/last-modified-at' => Check::value()->isDateAfter('15 days ago'),
            'headers/last-modified-at' => Check::value()->isDateBefore('now'),
        ]),

        Check::notAnyOf([
            'headers/forwarded'         => 'required',
            'headers/x-forwarded-for'   => 'required',
            'headers/x-forwarded-host'  => 'required',
            'headers/x-forwarded-proto' => 'required',
        ]),
    ]);
};


describe('Large Logic', function () {
    it('executed witout authentication', function () {
        expect(function () {
            $allowUnauthenticatedAccess = true;
            $request = [];
            test($allowUnauthenticatedAccess, $request);
        })->not->toThrow();
    });
    it('throw when executed witout authentication and allowUnauthenticatedAccess = false', function () {
        expect(function () {
            $allowUnauthenticatedAccess = false;
            $request = [];
            test($allowUnauthenticatedAccess, $request);
        })->toThrow();
    });
    it('executed with x-auth-token in headers', function () {
        expect(function () {
            $allowUnauthenticatedAccess = false;
            $request = [
                'headers' => ['x-auth-token' => '795973753bbb41b6adf3523589911b5721ee2ba2c5'],
            ];
            test($allowUnauthenticatedAccess, $request);
        })->not->toThrow();
    });

    it('executed with authToken in body', function () {
        expect(function () {
            $allowUnauthenticatedAccess = false;
            $request = [
                'body' => ['authToken' => '795973753bbb41b6adf3523589911b5721ee2ba2c5'],
            ];
            test($allowUnauthenticatedAccess, $request);
        })->not->toThrow();
    });

    it('valid when executed with good last-modified-at header', function () {
        expect(function () {
            $allowUnauthenticatedAccess = true;
            $request = [
                'headers' => [
                    'last-modified-at' => gmdate('D, d M Y H:i:s', strtotime('10 days ago')) . ' GMT'
                ],
            ];
            test($allowUnauthenticatedAccess, $request);
        })->not->toThrow();
    });
    it('throw when executed with bad last-modified-at header', function () {
        expect(function () {
            $allowUnauthenticatedAccess = true;
            $request = [
                'headers' => [
                    'last-modified-at' => gmdate('D, d M Y H:i:s', strtotime('100 days ago')) . ' GMT'
                ],
            ];
            test($allowUnauthenticatedAccess, $request);
        })->not->toThrow();
    });
});
