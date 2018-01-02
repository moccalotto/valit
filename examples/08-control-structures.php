<?php

use Valit\Check;
use Valit\Ensure;

require __DIR__ . '/../vendor/autoload.php';

$age = 42;

/**
 * Either age must be null or it must be a natureal number between 18 and 100
 */
Ensure::oneOf([
    Check::that($age)->isNull(),
    Check::that($age)->isNaturalNumber()->isGreaterThanOrEqual(18)->isLessThanOrEqual(100)
]);

print 'No Exceptions thrown. All assertions ok';

/*
WORKING WITH CONTROL STRUCTURES
===============================

The `Check` and `Ensure` facades have a number of functions to
help you create control structures.

| --------------------- | ------------------------------------------------- |
| Method                | Description                                       |
| --------------------- | ------------------------------------------------- |
| `oneOf($scenarios)`   | Exactly one of the given scenarios must pass      |
| `anyOf($scenarios)`   | At least one of the given scenarios must pass     |
| `allOf($scenarios)`   | All of the given scenarios must pass              |
| `noneOf($scenarios)`  | None of the given scenrarios may pass             |
| `not($scenario)`      | The given scenario may not pass                   |
| --------------------- | ------------------------------------------------- |

*/
