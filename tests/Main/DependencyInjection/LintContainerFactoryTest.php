<?php

declare(strict_types=1);

namespace Main\DependencyInjection;

use Exception;
use Wundii\PHPLint\Config\LintConfig;
use Wundii\PHPLint\Config\OptionEnum;
use Wundii\PHPLint\DependencyInjection\LintContainerFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LintContainerFactoryTest extends TestCase
{
    /**
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testCreateFromArgvInputReturnsContainerInterface()
    {
        $argvInput = $this->createMock(ArgvInput::class);

        $factory = new LintContainerFactory();
        $container = $factory->createFromArgvInput($argvInput);

        $this->assertInstanceOf(ContainerInterface::class, $container);
        $this->assertEquals('php', $container->get(LintConfig::class)->getString(OptionEnum::PHP_CGI_EXECUTABLE));
    }

    /**
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testCreateFromArgvInputWithDifferentLintConfigFile()
    {
        $argvInput = $this->createMock(ArgvInput::class);
        $argvInput->method('hasParameterOption')->willReturn(true);
        $argvInput->method('getParameterOption')->willReturn(__DIR__ . '/Files/phplint.php');

        $factory = new LintContainerFactory();
        $container = $factory->createFromArgvInput($argvInput);

        $this->assertInstanceOf(ContainerInterface::class, $container);
        $this->assertEquals('TimTest', $container->get(LintConfig::class)->getString(OptionEnum::PHP_CGI_EXECUTABLE));
    }
}
