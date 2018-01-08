<?php

use Valit\Check;
use Valit\Ensure;

require __DIR__ . '/../vendor/autoload.php';

$age = 31;
$productType = 'tobacco';

Ensure::allOf([
    // The product type must be a string.
    Check::that($productType)->isString(),

    // Age must be null or a natural number
    Check::oneOf([
        Check::that($age)->isNull(),
        Check::that($age)->isNaturalNumber()->isLessThanOrEqual(100),
    ]),

    // If the product type is tobacco or alcohol then the age must be at least 18
    Check::ifThen(
        Check::that($productType)->isOneOf('alcohol', 'tobacco'),
        Check::that($age)->isGreaterThanOrEqual(18)
    ),
]);

print 'No exceptions throw, all assertions passed';

/*
WORKING WITH CONTROL STRUCTURES
===============================

The `Check` and `Ensure` facades have a number of functions to
help you create control structures.

|------------------------------------------ | --------------------------------------------------|
| Method                                    | Description                                       |
|------------------------------------------ | --------------------------------------------------|
| `oneOf($scenarios)`                       | Exactly one of the given scenarios must pass      |
| `anyOf($scenarios)`                       | At least one of the given scenarios must pass     |
| `allOf($scenarios)`                       | All of the given scenarios must pass              |
| `noneOf($scenarios)`                      | None of the given scenrarios may pass             |
| `not($scenario)`                          | The given scenario may not pass                   |
| `allOrNone($scenario)`                    | All or none of the scenarios must pass            |
| `ifThen($condition, $then)`               | If $condition then $then else success             |
| `ifThenElse($condition, $then, $else)`    | If $condition then $then else $else               |
|------------------------------------------ | --------------------------------------------------|

*/
