<?php

use Valit\Check;
use Valit\Ensure;

require __DIR__ . '/../vendor/autoload.php';

$age = 42;

Ensure::oneOf([
    Check::that($age)->isNull(),
    Check::that($age)->isNaturalNumber()->isGreaterThanOrEqual(18)->isLessThanOrEqual(100)
]);

print 'All assertions ok';
