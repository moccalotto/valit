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
$commandLine->option('coverage-coveralls', 'default', 'coveralls.json');


// Apply the logic to the `'reporting'` entry point.
Filters::apply($this, 'reporting', function($chain) {

    // Get the reporter called `'coverage'` from the list of reporters
    $reporter = $this->reporters()->get('coverage');

    // Abort if no coverage is available.
    if (!$reporter || !$this->commandLine()->exists('coverage-coveralls')) {
        return $chain->next();
    }

    // Use the `Coveralls` class to write the JSON coverage into a file
    Coveralls::write([
        'collector' => $reporter,
        'file' => $this->commandLine()->get('coverage-coveralls'),
        'service_name' => 'travis-ci',
        'service_job_id' => getenv('TRAVIS_JOB_ID') ?: null
    ]);

    // Continue the chain
    return $chain->next();
});
