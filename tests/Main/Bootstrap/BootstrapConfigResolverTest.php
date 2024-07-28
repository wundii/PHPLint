<?php

declare(strict_types=1);

namespace Wundii\PHPLint\Tests\Main\Bootstrap;

use Exception;
use Wundii\PHPLint\Bootstrap\BootstrapConfig;
use Wundii\PHPLint\Bootstrap\BootstrapConfigResolver;
use Wundii\PHPLint\Bootstrap\BootstrapInputResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;

class BootstrapConfigResolverTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetBootstrapConfigWithValidConfig()
    {
        $configFile = __DIR__ . '/Files/phplint-01.php';

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '--config', $configFile]));
        $resolver = new BootstrapConfigResolver($inputResolver);

        $bootstrapConfig = $resolver->getBootstrapConfig();

        $this->assertInstanceOf(BootstrapConfig::class, $bootstrapConfig);
        $this->assertEquals($configFile, $bootstrapConfig->getBootstrapConfigFile());
    }

    /**
     * @throws Exception
     */
    public function testGetBootstrapConfigWithConfigFilePathEmpty()
    {
        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '--config']));
        $resolver = new BootstrapConfigResolver($inputResolver);

        $bootstrapConfig = $resolver->getBootstrapConfig();

        $this->assertInstanceOf(BootstrapConfig::class, $bootstrapConfig);
        $this->assertEquals(getcwd() . '/phplint.php', $bootstrapConfig->getBootstrapConfigFile());
    }

    /**
     * @throws Exception
     */
    public function testGetBootstrapConfigWithFileDoesNotExist()
    {
        $configFile = __DIR__ . '/Files/phplint-no-exist.php';

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '--config', $configFile]));
        $resolver = new BootstrapConfigResolver($inputResolver);

        $bootstrapConfig = $resolver->getBootstrapConfig();

        $this->assertInstanceOf(BootstrapConfig::class, $bootstrapConfig);
        $this->assertNull($bootstrapConfig->getBootstrapConfigFile());
    }
}
