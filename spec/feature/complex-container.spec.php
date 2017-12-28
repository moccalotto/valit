<?php

namespace Kahlan\Spec\Suite;

use Valit\Value;
use Valit\Check;
use Valit\Ensure;

function containerFeatureTest($request)
{
    Ensure::that($request)
        ->as('request')
        ->isAssociative()
        ->contains([
            'headers' => 'isArray',
            'headers/accept' => 'startsWith("application/json")',
            'body' => 'isArray',
            'body/authToken' => 'isUuid',
            'body/action' => Check::value()->matches('/[a-z][a-zA-Z0-9_]*$/A'),
            'body/params' => Value::isArray(),
            'body/extras' => 'optional & isArray',
        ])->isArray();  // this check is a bit silly, but it is proof-of-concept
}

describe('Container Feature Test', function () {
    it('throws if body is missing', function () {
        $closure = function () {
            $request = [
                'headers' => [
                    'accept' => 'application/json',
                ],
            ];
            containerFeatureTest($request);
        };

        expect($closure)->toThrow();
    });
    it('throws if headers are missing', function () {
        $closure = function () {
            $request = [
                'body' => [
                    'authToken' => '7c112694-6c8b-4f91-96e0-0f709f6e2274',
                    'action' => 'thingy',
                    'params' => [
                        'some' => 'thing',
                    ],
                ],
            ];
            containerFeatureTest($request);
        };

        expect($closure)->toThrow();
    });
    it('throws if body/authToken is not a valid Uuid', function () {
        $closure = function () {
            $request = [
                'headers' => [
                    'accept' => 'application/json; charset=utf-8',
                ],
                'body' => [
                    'authToken' => '7c1126946c8b4f9196e00f709f6e2274',
                    'action' => 'thingy',
                    'params' => [
                        'some' => 'thing',
                    ],
                ],
            ];
            containerFeatureTest($request);
        };

        expect($closure)->toThrow();
    });
    it('works', function () {
        $closure = function () {
            $request = [
                'headers' => [
                    'accept' => 'application/json; charset=utf-8',
                ],
                'body' => [
                    'authToken' => '7c112694-6c8b-4f91-96e0-0f709f6e2274',
                    'action' => 'thingy',
                    'params' => [
                        'some' => 'thing',
                    ],
                    'extras' => [
                        'foo' => 'bar'
                    ],
                ],
            ];
            containerFeatureTest($request);
        };

        expect($closure)->not->toThrow();
    });
});
