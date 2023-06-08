<?php

declare(strict_types=1);

namespace PHPLint\DependencyInjection;

use Exception;
use PHPLint\Bootstrap\BootstrapConfig;
use PHPLint\Bootstrap\BootstrapConfigInitializer;
use PHPLint\Bootstrap\BootstrapConfigRequirer;
use PHPLint\Bootstrap\BootstrapConfigResolver;
use PHPLint\Config\LintConfig;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class LintContainerFactory
{
    /**
     * @throws Exception
     */
    public function createFromArgvInput(ArgvInput $argvInput): ContainerInterface
    {
        $bootstrapConfigResolver = new BootstrapConfigResolver();
        $bootstrapConfig = $bootstrapConfigResolver->getBootstrapConfig($argvInput);
        $bootstrapConfigRequirer = new BootstrapConfigRequirer($bootstrapConfig);

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register(BootstrapConfig::class, BootstrapConfig::class)
            ->setPublic(true)
            ->setArgument('$bootstrapConfigFile', $bootstrapConfig->getBootstrapConfigFile());
        $containerBuilder->autowire(BootstrapConfigInitializer::class, BootstrapConfigInitializer::class)
            ->setPublic(true);
        $containerBuilder->autowire(BootstrapConfigResolver::class, BootstrapConfigResolver::class)
            ->setPublic(true);
        $containerBuilder->autowire(LintConfig::class, LintConfig::class)
            ->setPublic(true);

        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__));
        $phpFileLoader->load(__DIR__ . '/../../config/config.php');

        $containerBuilder->compile();

        $lintConfig = $containerBuilder->get(LintConfig::class);
        if ($lintConfig instanceof LintConfig) {
            $bootstrapConfigRequirer->loadConfigFile($lintConfig);
        }

        return $containerBuilder;
    }
}
