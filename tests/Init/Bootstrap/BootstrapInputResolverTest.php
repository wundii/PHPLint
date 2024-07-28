<?php

declare(strict_types=1);

namespace Wundii\PHPLint\Tests\Init\Bootstrap;

use Wundii\PHPLint\Bootstrap\BootstrapInputResolver;
use Wundii\PHPLint\Console\OptionEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;

class BootstrapInputResolverTest extends TestCase
{
    public function testHasOptionModeNone()
    {
        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '--fail']));
        $this->assertFalse($inputResolver->hasOption(OptionEnum::HELP));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '-f']));
        $this->assertFalse($inputResolver->hasOption(OptionEnum::HELP));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '--help']));
        $this->assertTrue($inputResolver->hasOption(OptionEnum::HELP));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '-h']));
        $this->assertTrue($inputResolver->hasOption(OptionEnum::HELP));
    }

    public function testHasOptionModeRequire()
    {
        $configFile = __DIR__ . '/Files/phplint-01.php';

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '--fail', $configFile]));
        $this->assertFalse($inputResolver->hasOption(OptionEnum::CONFIG));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '-f', $configFile]));
        $this->assertFalse($inputResolver->hasOption(OptionEnum::CONFIG));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '--config', $configFile]));
        $this->assertTrue($inputResolver->hasOption(OptionEnum::CONFIG));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '-c', $configFile]));
        $this->assertTrue($inputResolver->hasOption(OptionEnum::CONFIG));
    }

    public function testGetOptionValueModeRequire()
    {
        $configFile = __DIR__ . '/Files/phplint-01.php';

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '--fail', $configFile]));
        $this->assertNull($inputResolver->getOptionValue(OptionEnum::CONFIG));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '-f', $configFile]));
        $this->assertNull($inputResolver->getOptionValue(OptionEnum::CONFIG));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '--config', $configFile]));
        $this->assertEquals($configFile, $inputResolver->getOptionValue(OptionEnum::CONFIG));

        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '-c', $configFile]));
        $this->assertEquals($configFile, $inputResolver->getOptionValue(OptionEnum::CONFIG));
    }

    public function testGetOptionArrayModeRequireWithValueIsArray()
    {
        $path1 = 'srv/';
        $path2 = 'vendor/';

        $_SERVER['argv'] = ['bin/phplint', '--fail=' . $path1, '--fail=' . $path2];
        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '--fail=' . $path1, '--fail=' . $path2]));
        $this->assertCount(0, $inputResolver->getOptionArray(OptionEnum::PATHS));

        $_SERVER['argv'] = ['bin/phplint', '--paths ' . $path1, '--paths ' . $path2];
        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '--fail=' . $path1, '--fail=' . $path2]));
        $this->assertCount(0, $inputResolver->getOptionArray(OptionEnum::PATHS));

        $_SERVER['argv'] = ['bin/phplint', '--paths=' . $path1, '--paths=' . $path2];
        $inputResolver = new BootstrapInputResolver(new ArgvInput(['bin/phplint', '--path=' . $path1, '--path=' . $path2]));
        $this->assertEquals([$path1, $path2], $inputResolver->getOptionArray(OptionEnum::PATHS));

        unset($_SERVER['argv']);
    }
}
