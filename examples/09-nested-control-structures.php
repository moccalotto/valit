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
    Check::oneOf([
        Check::that($productType)->isNotOneOf(['alcohol', 'tobacco']),
        Check::allOf([
            Check::that($productType)->isOneOf(['alcohol', 'tobacco']),
            Check::that($age)->isGreaterThanOrEqual(18)
        ]),
    ])
]);

print 'No exceptions throw, all assertions passed';

/*
WORKING WITH CONTROL STRUCTURES
===============================

The `Check` and `Ensure` facades have a number of functions to
help you create control structures.

| ------------------------- | ------------------------------------------------- |
| Method                    | Description                                       |
| ------------------------- | ------------------------------------------------- |
| `oneOf($scenarios)`       | Exactly one of the scenarios must pass            |
| `anyOf($scenarios)`       | At least one of the scenarios must pass           |
| `allOf($scenarios)`       | All of the scenarios must pass                    |
| `noneOf($scenarios)`      | None of the scenrarios may pass                   |
| `allOrNone($scenarios)`   | Either all or none of the scenrarios may pass     |
| `not($scenario)`          | The given scenario may not pass                   |
| ------------------------- | ------------------------------------------------- |

*/
