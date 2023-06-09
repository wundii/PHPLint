<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Bootstrap;

use Exception;
use PHPLint\Bootstrap\BootstrapConfig;
use PHPLint\Bootstrap\BootstrapConfigResolver;
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

        $resolver = new BootstrapConfigResolver();
        $input = new ArgvInput(['bin/phplint', '--config', $configFile]);

        $bootstrapConfig = $resolver->getBootstrapConfig($input);

        $this->assertInstanceOf(BootstrapConfig::class, $bootstrapConfig);
        $this->assertEquals($configFile, $bootstrapConfig->getBootstrapConfigFile());
    }

    /**
     * @throws Exception
     */
    public function testGetBootstrapConfigWithConfigFilePathEmpty()
    {
        $resolver = new BootstrapConfigResolver();
        $input = new ArgvInput(['bin/phplint', '--config']);

        $bootstrapConfig = $resolver->getBootstrapConfig($input);

        $this->assertInstanceOf(BootstrapConfig::class, $bootstrapConfig);
        $this->assertEquals(getcwd() . '/phplint.php', $bootstrapConfig->getBootstrapConfigFile());
    }

    /**
     * @throws Exception
     */
    public function testGetBootstrapConfigWithFileDoesNotExist()
    {
        $configFile = __DIR__ . '/Files/phplint-no-exist.php';

        $resolver = new BootstrapConfigResolver();
        $input = new ArgvInput(['bin/phplint', '--config', $configFile]);

        $bootstrapConfig = $resolver->getBootstrapConfig($input);

        $this->assertInstanceOf(BootstrapConfig::class, $bootstrapConfig);
        $this->assertNull($bootstrapConfig->getBootstrapConfigFile());
    }
}
