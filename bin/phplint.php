<?php

declare(strict_types=1);

use PHPLint\Bootstrap\BootstrapConfigRequirer;
use PHPLint\Bootstrap\BootstrapConfigResolver;
use PHPLint\Config\LintConfig;
use PHPLint\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

@ini_set('memory_limit', '-1');

error_reporting(E_ALL);
ini_set('display_errors', 'stderr');
gc_disable();

require_once getcwd() . '/vendor/autoload.php';

$configResolver = new BootstrapConfigResolver();

try {
    $bootstrapConfig = $configResolver->getBootstrapConfig(new ArgvInput());
    $bootstrapConfigRequirer = new BootstrapConfigRequirer($bootstrapConfig);
    $lintConfig = $bootstrapConfigRequirer->getLintConfig();

    $application = new Application($lintConfig);
    exit($application->run());
} catch (Exception $e) {
    $application = new Application(new LintConfig());
    exit($application->runExceptionally($e));
}