<?php

use Kahlan\Filter\Filters;
use Kahlan\Reporter\Coverage\Exporter\Coveralls;

if (!extension_loaded('xdebug')) {
    return;
}

// Override certain defaults if xdebug is enabled.
$commandLine = $this->commandLine();
$commandLine->option('coverage', 'default', 1);
// $commandLine->option('coverage-scrutinizer', 'default', 'scrutinizer.xml');
// $commandLine->option('coverage-coveralls', 'default', 'coveralls.json');
//
// Filters::apply($this, 'reporting', function ($chain) {
//
//     // Get the reporter called `'coverage'` from the list of reporters
//     $reporter = $this->reporters()->get('coverage');
//
//     // Abort if no coverage is available.
//     if (!$reporter || !$this->commandLine()->exists('coverage-coveralls')) {
//         return $chain->next();
//     }
//
//     // Use the `Coveralls` class to write the JSON coverage into a file
//     Coveralls::write([
//         'collector' => $reporter,
//         'file' => $this->commandLine()->get('coverage-coveralls'),
//         'service_name' => 'travis-ci',
//         'service_job_id' => getenv('TRAVIS_JOB_ID') ?: null,
//     ]);
//
//     // Continue the chain
//     // return $chain->next();
// });
