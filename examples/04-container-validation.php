<?php

use Valit\Value;
use Valit\Ensure;

require __DIR__ . '/../vendor/autoload.php';

$assertions =  [
    'username'      => 'stringWhereLength("≤", 255) & longerThan(2)',
    'password'      => 'string & shorterThan(65536) & longerThan(4)',
    'remember_me'   => 'optional & oneOf(["yes", "no"])',
    'csrf_token'    => 'hexString & hasLength(40)',
];

$container = [
    'username' => 'foobar',
    'password' => 'secr37',
    'remember_me' => 'yes',
    'csrf_token' => '4f0a8c629e23d947bb369cf420607947c24dc9a9',
];

$checks = Ensure::that($container)->contains($assertions);

print 'No exceptions thrown, all fields are valid' . PHP_EOL;

print PHP_EOL;
print PHP_EOL;

print 'STATUS MESSAGES' . PHP_EOL;
print '===============' . PHP_EOL;
print_r(
    $checks->statusMessages()
);

/*
    STATUS MESSAGES
    ===============
    Array
    (
        [0] => PASS: username must be present
        [1] => PASS: username must have the type(s) "string"
        [2] => PASS: username must be a string that is shorter than 256 characters
        [3] => PASS: username must be a string that is longer than 2 characters
        [4] => PASS: password must be present
        [5] => PASS: password must have the type(s) "string"
        [6] => PASS: password must be a string that is shorter than 65536 characters
        [7] => PASS: password must be a string that is longer than 4 characters
        [8] => PASS: remember_me must be one of "yes", "no"
        [9] => PASS: csrf_token must be present
        [10] => PASS: csrf_token must contain only hexidecimal characters
        [11] => PASS: csrf_token must be a string that has the length 40
    )
 */


/*
WORKING WITH CONTAINER VALIDATION RESULTS
=========================================

The `ContainerValidator` has a number of utility methods you
can use to get info about the results of validating the container.

| ------------------------- | ----------------------------------------------------------------- |
| Method                    | Description                                                       |
| ------------------------- | ----------------------------------------------------------------- |
| `errors()`                | Array of failed AssertionResult objects.                          |
| `results()`               | Array of all AssertionResult objects.                             |
| `errorMessages()`         | Array of error messages for all fields.                           |
| `errorMessagesByPath()`   | Array of error messages for a given field.                        |
| `statusMessages()`        | Array of status messages for all fields.                          |
| `statusMessagesByPath`    | Array of status messages for a given field.                       |
| ------------------------- | ----------------------------------------------------------------- |
*/
