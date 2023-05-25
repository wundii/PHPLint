<?php

declare(strict_types=1);

use PHPLint\Bootstrap\BootstrapConfigResolver;
use PHPLint\Console\LintApplication;
use PHPLint\DependencyInjection\LintContainerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;

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

        $this->loadIfNotLoadedYet($cwdVendorAutoload);
    }

    public function loadIfNotLoadedYet(string $file): void
    {
        if (! file_exists($file)) {
            return;
        }

        require_once $file;
    }
}

// https://tomasvotruba.com/blog/introducing-light-kernel-for-symfony-console-apps/

$lintContainerFactory = new LintContainerFactory();
try {
    $container = $lintContainerFactory->createFromArgvInput(new ArgvInput());
    $application = $container->get(LintApplication::class);
    exit($application->run());
} catch (Throwable $throwable) {
    LintApplication::runExceptionally($throwable);
}
