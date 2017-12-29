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
 * Using \Valit\Check to get a list of all all assertions
 */
$emailCheck = Check::that('Some.Email@Foo.Com')
    ->as('email')
    ->isEmail()
    ->isShorterThan(255)
    ->isLowercase();

if ($emailCheck->hasErrors()) {
    print_r(
        $emailCheck->statusMessages()
    );
    /*
     Array
     (
         [0] => PASS: email must be a syntax-valid email address
         [1] => PASS: email must be a string that is shorter than 255 characters
         [2] => FAIL: email must only contain lower case latin letters
     )
     */
}