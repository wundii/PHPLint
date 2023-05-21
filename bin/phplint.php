<?php

declare(strict_types=1);

use PHPLint\Console\Application;

@ini_set('memory_limit', '-1');

error_reporting(E_ALL);
ini_set('display_errors', 'stderr');
gc_disable();

require_once __DIR__ . '/../vendor/autoload.php';

$application = new Application();
exit($application->run());