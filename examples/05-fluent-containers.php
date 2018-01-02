<?php

use Valit\Value;
use Valit\Ensure;

require __DIR__ . '/../vendor/autoload.php';

$assertions =  [
    'username'      => Value::isString()->shorterThan(256)->longerThan(2),
    'password'      => Value::isString()->shorterThan(65536)->isLongerThan(4),
    'remember_me'   => Value::isOptional()->isOneOf(['yes', 'no']),
    'csrf_token'    => Value::isHexString()->hasLength(40),
];

$container = [
    'username' => 'foobar',
    'password' => 'secr37',
    'remember_me' => 'yes',
    'csrf_token' => '4f0a8c629e23d947bb369cf420607947c24dc9a9',
];


$checks = Ensure::that($container)->contains($assertions);

print 'No exceptions thrown, all fields are valid';
