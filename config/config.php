<?php

declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('PHPLint\\', __DIR__ . '/../src/')
        ->public()
        ->autowire()
        ->exclude([
            __DIR__ . '/../src/Bootstrap/',
            __DIR__ . '/../src/Cache/',
            __DIR__ . '/../src/Config/',
            __DIR__ . '/../src/DependencyInjection/',
            __DIR__ . '/../src/Process',
        ]);

    $services->set(ArgvInput::class);
    $services->set(ConsoleOutput::class);
    $services->set(Filesystem::class);
    $services->set(Finder::class);
    $services->set(SymfonyStyle::class);

    $services->alias(InputInterface::class, ArgvInput::class);
    $services->alias(OutputInterface::class, ConsoleOutput::class);
};
