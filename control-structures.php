<?php

/**
 * This file is part of the Valit package.
 *
 * @author    Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017 Kim Ravn Hansen
 * @license   MIT
 */
use Moccalotto\Valit\Facades\Check;
use Moccalotto\Valit\Facades\Ensure;
use Moccalotto\Valit\Exceptions\InvalidValueException;

require 'vendor/autoload.php';


Ensure::container($something)->passes([
    Check::oneOf([
        'headers/x-xsrf-token' => 'required & isHexString & hasLength(42)',
        'headers/x-csrf-token' => 'required & isHexString & hasLength(42)',
        'body/auth'            => 'required & isHexString & hasLength(42)',
    ]),

    Check::allOrNone([
        'headers/last-modified-at' => 'required',
        'headers/last-modified-at' => 'dateAfter("15 days ago")',
        'headers/last-modified-at' => 'dateBefore("now")',
    ]),

    // alternative syntax:
    'headers/last-modified-at' => Check::allOrNone('required & dateAfter("15 days ago") & dateBefore("now")')
]);

Ensure::oneOf([
    Check::that($age)->isGreaterThanOrEqual(18),
    Check::that($order->price)->equals(0),
]);

Ensure::oneOf(function ($age, $price) {
    $age->isGreaterThan(17);
    $order->isObject()->hasKey('price');
}, $age, $order->price);

Ensure::that($number, 'number', $v)->passesOneOf([
    $v->matches('/^0x[1-9a-f][0-9a-f]*$/'),
    $v->matches('/^0[1-7][0-7]*$/'),
    $v->isNumeric()->isGreaterThan(0),
]);

Ensure::that($number, 'number')->passesOneOf([
    Value::matches('/^0x[1-9a-f][0-9a-f]*$/'),
    Value::matches('/^0[1-7][0-7]*$/'),
    Value::isNumeric()->isGreaterThan(0),
]);
