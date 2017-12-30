<?php

use Valit\Value;
use Valit\Ensure;

require __DIR__ . '/../vendor/autoload.php';

$varCharSize = 256;
$textFieldSize = 65536;

$assertions =  [
    'name'      => Value::isString()->shorterThan($varCharSize),
    'email'     => Value::isEmail()->shorterThan($varCharSize),
    'age'       => Value::greaterThanOrEqual(18)->lowerThan(70),

    'orderLines'            => Value::isConventionalArray(),
    'orderLines/*'          => Value::isAssociativeArray(),
    'orderLines/*/id'       => Value::isUuid(),
    'orderLines/*/count'    => Value::isInt()->greaterThan(0)->lessThan(100),
    'orderLines/*/comments' => Value::optional()->isString()->shorterThan($textFieldSize),
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
        ],
    ],
];


$checks = Ensure::that($container)->contains($assertions);

print 'All fields are valid';
