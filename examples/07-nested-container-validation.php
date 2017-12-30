<?php

use Valit\Value;
use Valit\Ensure;

require __DIR__ . '/../vendor/autoload.php';

$assertions = [
    'name'      => 'string & shorterThan(100)',
    'email'     => 'email & shorterThan(255)',
    'address'   => 'string',
    'postcode'  => 'string',
    'age'       => 'optional & greaterThanOrEqual(18) & lowerThan(70)',

    'orderLines'                => 'conventionalArray',
    'orderLines/*'              => 'associative',
    'orderLines/*/productId'    => 'uuid',
    'orderLines/*/count'        => 'integer & greaterThan(0)',
    'orderLines/*/variant'      => 'optional & string',
];


$data = [
    'name' => 'Kim Hansen',
    'email' => 'foobar@example.com',
    'address' => '1337 foostreet',
    'postcode' => 'ZIP-42',
    // 'age' is optional, it will only be validated if present

    'orderLines' => [
        [
            'productId' => '16ddae5b-4776-42d2-ada1-45d520e35e9b',
            'count'     => 1,
            'variant'   => 'red',
        ],
        [
            'productId' => '818574f3-87da-4881-9f71-493780fbbcaf',
            'count'     => 1,
            // 'variant' is optional, it will only be validated if present.
        ],
    ],
];

Ensure::that($data)->contains($assertions);

print 'No exceptions thrown, all fields are valid';
