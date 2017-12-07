<?php

/**
 * This file is part of the Valit package.
 *
 * @author    Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017 Kim Ravn Hansen
 * @license   MIT
 */
use Valit\Facades\Check;
use Valit\Facades\Ensure;
use Valit\Exceptions\InvalidValueException;

require 'vendor/autoload.php';


Ensure::container($something)->passes([
    Check::oneOf([
        'headers/x-xsrf-token' => 'present & isHexString & hasLength(42)',
        'headers/x-csrf-token' => 'present & isHexString & hasLength(42)',
        'body/auth'            => 'present & isHexString & hasLength(42)',
        Check::that($someOtherVariable)->isTrue(),
    ]),

    Check::allOrNone([
        'headers/last-modified-at' => 'present',
        'headers/last-modified-at' => 'dateAfter("15 days ago")',
        'headers/last-modified-at' => 'dateBefore("now")',
    ]),

    Check::anyOf([
    ]),

    Check::notAnyOf([
        'headers/forwarded'         => 'present',
        'headers/x-forwarded-for'   => 'present',
        'headers/x-forwarded-host'  => 'present',
        'headers/x-forwarded-proto' => 'present',
    ]),

    // alternative syntax:
    'headers/last-modified-at' => Check::allOrNone('present & dateAfter("15 days ago") & dateBefore("now")')
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
