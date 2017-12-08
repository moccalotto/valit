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
    Check::oneOf([
        'headers/x-xsrf-token' => 'isHexString & hasLength(42)',
        'headers/x-csrf-token' => 'isHexString & hasLength(42)',
        'body/auth'            => 'isHexString & hasLength(42)',
        Check::that($someOtherVariable)->isTruthy(),
    ]),

    Check::allOrNone([
        'headers/last-modified-at' => 'required',
        'headers/last-modified-at' => 'dateAfter("15 days ago")',
        'headers/last-modified-at' => 'dateBefore("now")',
    ]),
    // alternative syntax:
    'headers/last-modified-at' => Check::allOrNone('required & dateAfter("15 days ago") & dateBefore("now")'),

    'headers/*' => Check::keys('matches("/^[a-z][a-z0-9-]*[a-z0-9]$/")'),

    Check::anyOf([
    ]),

    Check::notAnyOf([
        'headers/forwarded'         => 'required',
        'headers/x-forwarded-for'   => 'required',
        'headers/x-forwarded-host'  => 'required',
        'headers/x-forwarded-proto' => 'required',
    ]),

]);

Ensure::oneOf([
    Check::that($age)->isGreaterThanOrEqual(18),
    Check::that($order->price)->equals(0),
]);

Ensure::oneOf(function ($age, $price) {
    $age->isGreaterThan(17);
    $order->isObject()->hasKey('price');
}, $age, $order->price);

Ensure::that($number, 'number')->passesOneOf([
    Check::value()->matches('/^0x[1-9a-f][0-9a-f]*$/'),
    Check::value()->matches('/^0[1-7][0-7]*$/'),
    Check::value()->isNumeric()->isGreaterThan(0),
]);
