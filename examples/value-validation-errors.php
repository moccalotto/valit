<?php

use Valit\Check;

require __DIR__ . '/../vendor/autoload.php';

/*
-----------------------------------------------------------------
 WORKING WITH ERROR MESSAGES
-----------------------------------------------------------------

The `\Valit\Check` facade does not throw exceptions
if the value does not live up to the assertions,
rather you can use the following functions to
inspect the success and status of the check.

| --------------------- | ------------------------------------- |
| Method                | Description                           |
| --------------------- | ------------------------------------- |
| `success()`           | Did all assertions pass?              |
| `hasErrors()`         | Did one or more tests fail?           |
| `firstErrorMessage()` | The first error message (if any)      |
| `errorMessages()`     | Array of error messages.              |
| `statusMessages()     | Array of status message.              |
| `results()`           | Array of AssertionResult objects.     |
| --------------------- | ------------------------------------- |

*/




/*
 * Using \Valit\Check to get a list of failed exceptions.
 */
$ageCheck = Check::that('foo')          // use Check instead of Ensure to gain access to all error messages
    ->as('age')                         // used to render prettier error messages
    ->isNaturalNumber()                 // this fails because 'foo' is not a natural number
    ->isGreaterThanOrEqual(18)          // this fails because 'foo' is not ≥ 18
    ->isLowerThanOrEqual(75);           // this fails because 'foo' is not ≤ 75

if ($ageCheck->hasErrors()) {
    print_r(
        $ageCheck->errorMessages()
    );
    /*
     Array
     (
         [0] => age must be a natural number
         [1] => age must be greater than or equal to 18
         [2] => age must be less than 75
     )
     */
}
