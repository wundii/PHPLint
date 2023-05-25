<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

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
            __DIR__ . '/../src/Config/',
            __DIR__ . '/../src/DependencyInjection/',
        ]);
};