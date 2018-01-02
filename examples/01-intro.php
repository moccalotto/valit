<?php

use Valit\Ensure;

require __DIR__ . '/../vendor/autoload.php';

$age = 42;

Ensure::that($age)
    ->isNaturalNumber()
    ->isGreaterThanOrEqual(18)
    ->isLowerThanOrEqual(75);

print 'No exceptions throw, all assertions passed';
