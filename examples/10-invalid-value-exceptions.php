<?php

use Valit\Check;
use Valit\Exceptions\InvalidValueException;

require __DIR__ . '/../vendor/autoload.php';

$age = -600.66;

/*
 * Using check to throw an exception with all the failed checks.
 */
try {

    Check::that($age)
        ->as('entered age')
        ->isNaturalNumber()
        ->isGreaterThanOrEqual(18)
        ->isLowerThanOrEqual(75)
        ->orThrowException();

} catch (InvalidValueException $e) {

    print 'SHORT MESSAGE:' . PHP_EOL;
    print '==============' . PHP_EOL;
    print $e->getMessage();
    print PHP_EOL;
    print PHP_EOL;

    print 'DETAILED MESSAGE:' . PHP_EOL;
    print '=================' . PHP_EOL;
    print $e->detailedMessage();
}
/*
    SHORT MESSAGE:
    ==============
    Validation of entered age failed

    DETAILED MESSAGE:
    =================
    Validated of entered age failed the following requirements:
     * entered age must be a natural number.
     * entered age must be greater than or equal to 18.
 */

/*
WORKING WITH VALIDATION RESULTS
===============================

The `Valit\Exceptions\InvalidValueException` has the same
functionality as the `ValueValidator` that is returned
when you validate a value with the `Check` facade.
This means that you have access to all the following
utility functions on the exception object:

| --------------------- | ----------------------------------------------------------------- |
| Method                | Description                                                       |
| --------------------- | ----------------------------------------------------------------- |
| `getMessage()`        | The exception message.                                            |
| `detailedMessage()`   | A more detailed exception message.                                |
| `firstErrorMessage()` | The first error message.                                          |
| `errorMessages()`     | Array of error messages.                                          |
| `statusMessages()`    | Array of status message.                                          |
| `results()`           | Array of AssertionResult objects.                                 |
| --------------------- | ----------------------------------------------------------------- |
 */
