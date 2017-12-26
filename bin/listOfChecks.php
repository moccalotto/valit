#!/usr/bin/env php
<?php

/**
 * This file is part of the Valit package.
 *
 * Generate a json-encoded array containing all the checks supported by the default
 * Valit Manager.
 *
 * Each entry contains
 *  - name: string
 *  - headline: string
 *  - description: string
 *  - aliases: string[]
 *  - paramlist: string[]
 *
 * @package Valit
 * @author Kim Ravn Hansen <moccalotto@gmail.com>
 * @copyright 2017
 * @license MIT
 */

require __DIR__ . '/../vendor/autoload.php';

echo json_encode(
    Valit\Manager::instance()->checks(),
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);
