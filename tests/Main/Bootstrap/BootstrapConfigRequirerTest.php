<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Bootstrap;

use Exception;
use PHPLint\Bootstrap\BootstrapConfig;
use PHPLint\Bootstrap\BootstrapConfigRequirer;
use PHPLint\Config\LintConfig;
use PHPLint\Config\OptionEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BootstrapConfigRequirerTest extends TestCase
{
    public function getMockContainerBuilder(): ContainerBuilder
    {
        return new ContainerBuilder();
    }

    /**
     * @throws Exception
     */
    public function testGetLintConfigWithValidConfig()
    {
        $configFile = __DIR__ . '/Files/phplint-01.php';

        $bootstrapConfig = new BootstrapConfig($configFile);
        $requirer = new BootstrapConfigRequirer($bootstrapConfig);

        $lintConfig = new LintConfig();
        $lintConfig = $requirer->loadConfigFile($lintConfig);

        $this->assertInstanceOf(LintConfig::class, $lintConfig);
        $this->assertEquals('phpUnitTest', $lintConfig->getString(OptionEnum::PHP_CGI_EXECUTABLE));
    }

    public function testGetLintConfigWithInvalidConfigReturnString()
    {
        $configFile = __DIR__ . '/Files/phplint-02.php';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('BootstrapConfig ' . $configFile . ' file is not callable.');

        $bootstrapConfig = new BootstrapConfig($configFile);
        $requirer = new BootstrapConfigRequirer($bootstrapConfig);

        $lintConfig = new LintConfig();
        $requirer->loadConfigFile($lintConfig);
    }

    public function testGetLintConfigWithInvalidConfigReturnWithoutParameter()
    {
        $configFile = __DIR__ . '/Files/phplint-03.php';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('BootstrapConfig ' . $configFile . ' file has no parameters.');

        $bootstrapConfig = new BootstrapConfig($configFile);
        $requirer = new BootstrapConfigRequirer($bootstrapConfig);

        $lintConfig = new LintConfig();
        $requirer->loadConfigFile($lintConfig);
    }

    public function testGetLintConfigWithInvalidConfigReturnWithWrongParameter()
    {
        $configFile = __DIR__ . '/Files/phplint-04.php';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('BootstrapConfig ' . $configFile . ' file has no lintconfig parameter.');

        $bootstrapConfig = new BootstrapConfig($configFile);
        $requirer = new BootstrapConfigRequirer($bootstrapConfig);

        $lintConfig = new LintConfig();
        $requirer->loadConfigFile($lintConfig);
    }
}
