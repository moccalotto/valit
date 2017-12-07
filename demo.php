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

/*
|----------------------------------------------------------
| Teaser
|----------------------------------------------------------
*/

$age = 42;

Ensure::that($age)
    ->isNumeric()
    ->isGreaterThanOrEqual(18)
    ->isLowerThanOrEqual(75);

/*
|----------------------------------------------------------
| Validity
|----------------------------------------------------------
|
| You can determine if a variable passes all your criteria
| by using the `valid` method.
|
| Conversely you can use the `invalid` method to check if
| one or more checks did not pass.
|
*/

$x = 42; // the variable to validate

$valid = Check::that($x)
    ->isInt()                   // Success
    ->isGreaterThanOrEqual(42)  // Success
    ->isLessThan(100)           // Success
    ->valid();                  // true

var_dump($valid);  // bool(true)

/*
|----------------------------------------------------------
| Error Messages
|----------------------------------------------------------
|
| If you want to know precisely which checks failed, you
| can use the `errorMessages` method.
|
*/

$x = 42; // the variable to validate

$errors = Check::that($x)
    ->isNumeric()       // Success
    ->isFloat()         // Fail
    ->isCloseTo(40)     // Fail
    ->errorMessages();

print_r($errors);

/*
Array
(
    [0] => value must have the type "double"
    [1] => value must equal 40 with a margin of error of 1.0e-5
)
 */

/*
|----------------------------------------------------------
| Aliases
|----------------------------------------------------------
|
| If you display the error messages to the end user,
| you can tell them exactly which variable failed the
| validation by giving the value an alias. This is done
| via the `as` method. If you prefer, you can use the
| `alias` method, which does exactly the same.
|
*/

$email = 'foo@example.com';

$errors = Check::that($email)
    ->as('Your Email Address')
    ->isEmail()             // Success
    ->endsWith('.co.uk')    // Fail
    ->errorMessages();

print_r($errors);

/*
Array
(
    [0] => Your Email Address must end with the string ".co.uk"
)

// Notice it says "Your Email Address" rather than "value".
*/

/*
|----------------------------------------------------------
| Ensuring
|----------------------------------------------------------
|
| If you want to assert that all checks must pass, you can
| use the `Valit\Ensure` facade.
| If a single check fails, we throw a
| `Valit\Exceptions\InvalidValueException` that contains the
| error message for that check.
|
 */

$email = 'Doctor.Hansen@Example.com';

try {
    Ensure::that($x)
        ->as('Email')
        ->isEmail()             // Success
        ->isLowercase()         // Throws InvalidValueException
        ->endsWith('.co.uk');   // Not run
} catch (InvalidValueException $e) {
    var_dump($e->getMessage());
    /*
        string(42) "Email must be a syntax-valid email address"
     */
}

/*
|----------------------------------------------------------
| Ensuring all checks
|----------------------------------------------------------
|
| If you want to assert that all checks pass, and you want
| info about all tests, you can use the Check facade in
| combination with the `orThrowException` method.
|
| The thrown `InvalidValueException` will contain a list of
| all the error messages. These can be accessed via the
| `errorMessages` method like so:
|
*/

$age = '42.3';

try {
    Check::that($age)
        ->as('age')
        ->isNaturalNumber()     // Fail
        ->isGreaterThan(18)     // Success
        ->isLowerThan(30)       // Fail
        ->orThrowException();
} catch (InvalidValueException $e) {
    print_r($e->errorMessages());
    /*
        Array
        (
            [0] => age must be a natural number
            [1] => age must be less than 30
        )
     */
}

$request = [
    'name' => 'Kim Hansen',
    'email' => 'kim@wordwax.com',
    'age' => 80,
    'address' => 'street 42',
    'orderLines' => [
        [
            'productId' => 'dd4fbef0-0ece-4596-ab07-97a2d44aabaG',
            'count' => 52,
        ],
        [],     // empty arrays are neither associative nor numericly indexed.
    ],
];

$checks = Check::container($request)->passes([
    'name' => 'string & shorterThan(100)',
    'email' => 'email & shorterThan(255)',
    'address' => ['string'],
    'age' => ['optional', 'greaterThan' => [18], 'lowerThan(70)'],

    'orderLines' => 'conventionalArray',
    'orderLines/*' => 'associative',
    'orderLines/*/productId' => 'uuid',
    'orderLines/*/count' => 'optional & integer & greaterThan(0)',
]);

print_r($checks->errors());
/*
Array
(
    [age] => Array
        (
            [0] => age must be less than 70
        )

    [orderLines/1] => Array
        (
            [0] => orderLines/1 must be an associative array
        )

    [orderLines/0/productId] => Array
        (
            [0] => orderLines/0/productId must be a valid UUID
        )

)
*/

print_r($checks->errorMessagesByPath(['orderLines', 0, 'productId']));
/*
    Array
    (
        [0] => Field must be a valid UUID
    )
*/


try {
    Check::container([
        'a' => 1234,
        'b' => [
            'c' => 'g',
            'd' => 'h',
        ],

    ])
    ->as('foo')
    ->passes([
        'a' => 'required & isString & longerThan(100)',
        'b' => 'required & isArray',
        'b/c' => 'required & isInt & greaterThan(10)',
        'b/d' => 'required & isString',
        'b/e' => 'required',
        'c' => 'required & isString & longerThan(100)',
    ])->orThrowException();
} catch (\Exception $e) {
    print $e->getMessage();

    /*
        Container did not pass validation:
            a must have the type "string"
            a must be a string that is longer than 100
            b/c must have the type "integer"
            b/c must be greater than 10
            b/e is required
            c is required
     */
}
