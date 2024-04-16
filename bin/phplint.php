<?php

declare(strict_types=1);

use PHPLint\Console\LintApplication;
use PHPLint\DependencyInjection\LintContainerFactory;
use Symfony\Component\Console\Input\ArgvInput;

@ini_set('memory_limit', '-1');

error_reporting(E_ALL);
ini_set('display_errors', 'stderr');
gc_disable();

$autoloadIncluder = new AutoloadIncluder();
$autoloadIncluder->includeCwdVendorAutoloadIfExists();

final class AutoloadIncluder
{
    public function includeCwdVendorAutoloadIfExists(): void
    {
        $cwdVendorAutoload = getcwd() . '/vendor/autoload.php';
        if (! is_file($cwdVendorAutoload)) {
            return;
        }

        if (! file_exists($cwdVendorAutoload)) {
            return;
        }

        require_once $cwdVendorAutoload;
    }
}

$lintContainerFactory = new LintContainerFactory();
try {
    $container = $lintContainerFactory->createFromArgvInput(new ArgvInput());
    $application = $container->get(LintApplication::class);
    exit($application->run());
} catch (Throwable $throwable) {
    LintApplication::runExceptionally($throwable);
}
