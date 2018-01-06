<?php

use Kahlan\Filter\Filters;
use Kahlan\Reporter\Coverage\Exporter\Coveralls;

$commandLine = $this->commandLine();

$commandLine->option('spec', 'default', 'tests');
if (!extension_loaded('xdebug')) {
    return;
}

// Override certain defaults if xdebug is enabled.
$commandLine->option('coverage', 'default', 3);
$commandLine->option('clover', 'default', 'coverage.xml');
