<?php

declare(strict_types=1);

namespace Main\Bootstrap;

use Exception;
use PHPUnit\Framework\TestCase;
use Wundii\PHPLint\Bootstrap\BootstrapConfig;

class BootstrapConfigTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testValidBootstrapConfigFile()
    {
        $configFile = __DIR__ . '/Files/phplint-01.php';
        $bootstrapConfig = new BootstrapConfig($configFile);

        $this->assertEquals($configFile, $bootstrapConfig->getBootstrapConfigFile());
    }

    public function testNonexistentBootstrapConfigFile()
    {
        $configFile = __DIR__ . '/Files/phplint-no-exist.php';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('BootstrapConfig ' . $configFile . ' file does not exist.');

        new BootstrapConfig($configFile);
    }

    public function testInvalidBootstrapConfigFile()
    {
        $configFile = __DIR__ . '/Files/';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('BootstrapConfig ' . $configFile . ' file is not a file.');

        new BootstrapConfig($configFile);
    }
}
