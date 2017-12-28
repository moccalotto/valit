<?php

use Valit\Check;
use Valit\Ensure;

require __DIR__ . '/../vendor/autoload.php';

$age = 21;
$productType = 'tobacco';

// Age must always be defined, and must always be a natural number
Ensure::that($age)->isNaturalNumber();


// You must be over 21 to purchase alcohol or tobacco.
Ensure::anyOf([
    Check::that($age)->gte(21),
    Check::not(
        Check::that($productType)->isOneOf(['alcohol', 'tobacco'])
    )
]);

print 'All assertions ok';
