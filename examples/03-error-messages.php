<?php

use Valit\Check;

require __DIR__ . '/../vendor/autoload.php';

$userInput = '-66.5';

/*
 * Using \Valit\Check to get a list of failed assertions.
 */
$ageCheck = Check::that($userInput)     // use Check instead of Ensure to gain access to all error messages
    ->as('age')                         // as($alias) used to render prettier error messages
    ->isNaturalNumber()                 // this fails because -66.5 is not a natural number
    ->isGreaterThanOrEqual(18)          // this fails because -66.5 is not ≥ 18
    ->isLowerThanOrEqual(75);           // this succeeds because -66.5 ≤ 75

if ($ageCheck->hasErrors()) {
    print_r(
        $ageCheck->errorMessages()
    );
}
/*
    Array
    (
        [0] => age must be a natural number
        [1] => age must be greater than or equal to 18
    )
 */

/*
WORKING WITH VALIDATION RESULTS
===============================

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
