<?php

use Valit\Ensure;

require __DIR__ . '/../vendor/autoload.php';

$age = 42;

Ensure::that($age)
    ->isNumeric()
    ->isGreaterThanOrEqual(18)
    ->isLowerThanOrEqual(75);

print 'All assertions ok';
