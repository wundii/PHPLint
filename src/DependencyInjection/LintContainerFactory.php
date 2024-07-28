<?php

declare(strict_types=1);

namespace Wundii\PHPLint\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Wundii\PHPLint\Bootstrap\BootstrapConfig;
use Wundii\PHPLint\Bootstrap\BootstrapConfigInitializer;
use Wundii\PHPLint\Bootstrap\BootstrapConfigRequirer;
use Wundii\PHPLint\Bootstrap\BootstrapConfigResolver;
use Wundii\PHPLint\Bootstrap\BootstrapInputResolver;
use Wundii\PHPLint\Config\LintConfig;

final class LintContainerFactory
{
    /**
     * @throws Exception
     */
    public function createFromArgvInput(ArgvInput $argvInput): ContainerInterface
    {
        $bootstrapInputResolver = new BootstrapInputResolver($argvInput);
        $bootstrapConfigResolver = new BootstrapConfigResolver($bootstrapInputResolver);
        $bootstrapConfig = $bootstrapConfigResolver->getBootstrapConfig();
        $bootstrapConfigRequirer = new BootstrapConfigRequirer($bootstrapConfig);

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register(BootstrapConfig::class, BootstrapConfig::class)
            ->setPublic(true)
            ->setArgument('$bootstrapConfigFile', $bootstrapConfig->getBootstrapConfigFile());
        $containerBuilder->autowire(BootstrapConfigInitializer::class, BootstrapConfigInitializer::class)
            ->setPublic(true);
        $containerBuilder->autowire(BootstrapConfigResolver::class, BootstrapConfigResolver::class)
            ->setPublic(true);
        $containerBuilder->autowire(BootstrapInputResolver::class, BootstrapInputResolver::class)
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
