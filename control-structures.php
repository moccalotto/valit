<?php

/**
 * This file is part of the Valit package.
 *
 * @author    Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017 Kim Ravn Hansen
 * @license   MIT
 */
use Valit\Check;
use Valit\Ensure;
use Valit\Exceptions\InvalidValueException;

require 'vendor/autoload.php';

Ensure::container($something)->passes([
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
        'headers/last-modified-at' => 'dateAfter("15 days ago")',
        'headers/last-modified-at' => 'dateBefore("now")',
    ]),

    Check::notAnyOf([
        'headers/forwarded'         => 'required',
        'headers/x-forwarded-for'   => 'required',
        'headers/x-forwarded-host'  => 'required',
        'headers/x-forwarded-proto' => 'required',
        Check::oneOf(['foo' => 'bar'])
    ]),
]);

Ensure::oneOf([
    Check::that($age)->isGreaterThanOrEqual(18),
    Check::that($order->price)->equals(0),
]);

Ensure::that($number, 'number')->passesOneOf([
    Check::value()->matches('/^0x[1-9a-f][0-9a-f]*$/'),
    Check::value()->matches('/^0[1-7][0-7]*$/'),
    Check::value()->isNumeric()->isGreaterThan(0),
]);
