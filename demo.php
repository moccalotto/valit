<?php

use Moccalotto\Valit\Facades\Check;

require 'vendor/autoload.php';

$results = Check::that(87)
    ->as('myNumber')
    ->isNumeric()
    ->isPositive()
    ->isDivisibleBy(29)
    ->isPrimeRelativeTo(20)
    ->isArray()
    ->isString()
    ->isObject()
    ->renderedResults();

print_r($results);
