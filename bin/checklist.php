<?php

require __DIR__ . '/../vendor/autoload.php';

echo json_encode(Moccalotto\Valit\Manager::instance()->checks(), JSON_PRETTY_PRINT);
