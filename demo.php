<?php

/*
 * This file is part of the Valit package.
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2016
 * @license MIT
 */

use Moccalotto\Valit\Facades\Check;
use Moccalotto\Valit\Facades\Ensure;
use Moccalotto\Valit\ValidationException;

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
| use the `Moccalotto\Valit\Ensure` facade.
| If a single check fails, we throw a
| `Moccalotto\Valit\ValidationException` that contains the
| error message for that check.
|
 */

$email = 'Doctor.Hansen@Example.com';

try {
    Ensure::that($x)
        ->as('Email')
        ->isEmail()             // Success
        ->isLowercase()         // Throws ValidationException
        ->endsWith('.co.uk');   // Not run
} catch (ValidationException $e) {
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
| The thrown `ValidationException` will contain a list of
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
} catch (ValidationException $e) {
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
    'age' => 40,
    'address' => 'street 42',
    'orderLines' => [
        [
            'productId' => 'dd4fbef0-0ece-4596-ab07-97a2d44aabaG',
            'count' => 52,
        ],
    ],
];

$checks = Check::container($request)->against([
    'name' => 'required & string & shorterThan(100)',
    'email' => 'required & email & shorterThan(255)',
    'address' => ['required', 'string'],
    'age' => ['greaterThan' => [35], 'lowerThan(50)', 'divisibleBy' => 4],

    'orderLines' => 'required & conventionalArray',
    'orderLines/*/productId' => 'required & uuid',
    'orderLines/*/count' => 'integer & greaterThan(0)',
]);


print_r($checks->errors());
/*
Array
(
    [orderLines/0/productId] => Array
    (
        [0] => Field must be a valid UUID
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


