<?php

declare(strict_types=1);

use PHPLint\Bootstrap\BootstrapConfig;
use PHPLint\Bootstrap\BootstrapConfigRequirer;
use PHPLint\Config\LintConfig;
use PHPUnit\Framework\TestCase;

class BootstrapConfigRequirerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetLintConfigWithValidConfig()
    {
        $configFile = __DIR__ . '/Files/phplint-01.php';

        $bootstrapConfig = new BootstrapConfig($configFile);
        $requirer = new BootstrapConfigRequirer($bootstrapConfig);

        $lintConfig = $requirer->getLintConfig();

        $this->assertInstanceOf(LintConfig::class, $lintConfig);
        $this->assertSame('php', $lintConfig->getPhpCgiExecutable());
        $this->assertSame([getcwd() . DIRECTORY_SEPARATOR], $lintConfig->getPaths());
        $this->assertSame([], $lintConfig->getSkip());
        $this->assertSame([], $lintConfig->getSets());
    }

    public function testGetLintConfigWithInvalidConfigReturnString()
    {
        $configFile = __DIR__ . '/Files/phplint-02.php';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("BootstrapConfig " . $configFile . " file is not callable.");

        $bootstrapConfig = new BootstrapConfig($configFile);
        $requirer = new BootstrapConfigRequirer($bootstrapConfig);

        $requirer->getLintConfig();
    }

    public function testGetLintConfigWithInvalidConfigReturnWithoutParameter()
    {
        $configFile = __DIR__ . '/Files/phplint-03.php';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("BootstrapConfig " . $configFile . " file has no parameters.");

        $bootstrapConfig = new BootstrapConfig($configFile);
        $requirer = new BootstrapConfigRequirer($bootstrapConfig);

        $requirer->getLintConfig();
    }

    public function testGetLintConfigWithInvalidConfigReturnWithWrongParameter()
    {
        $configFile = __DIR__ . '/Files/phplint-04.php';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("BootstrapConfig " . $configFile . " file has no lintconfig parameter.");

        $bootstrapConfig = new BootstrapConfig($configFile);
        $requirer = new BootstrapConfigRequirer($bootstrapConfig);

        $requirer->getLintConfig();
    }
}