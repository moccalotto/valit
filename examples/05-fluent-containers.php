<?php

use Valit\Value;
use Valit\Check;

require __DIR__ . '/../vendor/autoload.php';

$varCharLength = 255;
$textFieldLength = 65535;
$sha1Length = 40;
$rememberOptions = ['yes', 'no', '1', '0', ''];

$assertions =  [
    'username'      => Value::lengthIs('<=', $varCharLength)->longerThan(2),
    'password'      => Value::lengthIs('<=', $textFieldLength)->longerThan(4),
    'remember_me'   => Value::isOptional()->isOneOf($rememberOptions),
    'csrf_token'    => Value::isHexString()->hasLength($sha1Length),
];

$postData = [
    'username' => 'foobar',
    'password' => 'secr37',
    'remember_me' => 'foo',
    'csrf_token' => 'this is not hex',
];


$checks = Check::that($postData)->contains($assertions);

print_r(
    $checks->errorMessages()
);

/*
    Array
    (
        [0] => remember_me must be one of "yes", "no", "1", "0", ""
        [1] => csrf_token must contain only hexidecimal characters
        [2] => csrf_token must be a string where length is 40
    )
 */
