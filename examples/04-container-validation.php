<?php

use Valit\Value;
use Valit\Ensure;

require __DIR__ . '/../vendor/autoload.php';

$assertions =  [
    'name'      => 'string & shorterThan(255)',
    'email'     => 'email & shorterThan(255)',
    'age'       => 'greaterThan(17) & lessThan(100)',

    'orderLines'            => 'array',
    'orderLines/*'          => 'associativeArray',
    'orderLines/*/id'       => 'uuid',
    'orderLines/*/count'    => 'int & greaterThan(0) & lessThan(100)',
    'orderLines/*/comments' => 'optional & string & shorterThan(65536)',
];

$container = [
    'name' => 'Kim Hansen',
    'email' => 'foo@example.com',
    'address' => 'Mt Everest Street 1337',
    'age' => 65,

    'orderLines' => [
        [
            'id' => '053e54ab-ead9-49e3-bb5b-af550cb0c20e',
            'count' => 1,
            'comments' => 'I love this product',
        ],
        [
            'id' => 'd9e918a8-e32a-4ccb-b929-4bd273c6f06f',
            'count' => 2,
            // the 'comments' field is optional, so we don't need it
            // it will only be validated (string & shorterThan(65536)) if
            // it is present.
        ],
    ],
];

$checks = Ensure::that($container)->contains($assertions);

print 'No exceptions cast, all assertions passed!';
