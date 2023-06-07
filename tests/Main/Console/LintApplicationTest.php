<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Console;

use Exception;
use PHPLint\Bootstrap\BootstrapConfigInitializer;
use PHPLint\Bootstrap\BootstrapConfigResolver;
use PHPLint\Config\LintConfig;
use PHPLint\Console\Commands\LintCommand;
use PHPLint\Console\Commands\LintInitCommand;
use PHPLint\Console\LintApplication;
use PHPLint\Console\Output\LintConsoleOutput;
use PHPLint\Finder\LintFinder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;

class LintApplicationTest extends TestCase
{
    public function getMockContainerBuilder(): ContainerBuilder
    {
        return new ContainerBuilder();
    }

    /**
     * @throws Exception|\PHPUnit\Framework\MockObject\Exception
     */
    public function testRun()
    {
        $consoleInput = new ArgvInput();
        $consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));
        $symfonyStyle = new SymfonyStyle($consoleInput, $consoleOutput);
        $lintConsoleOutput = new LintConsoleOutput($symfonyStyle);
        $container = $this->createMock(ContainerBuilder::class);
        $bootstrapConfigInitializer = new BootstrapConfigInitializer(new Filesystem(), $symfonyStyle);
        $bootstrapConfigResolver = new BootstrapConfigResolver();
        $lintConfig = new LintConfig($container);
        $lintConfig->setPaths(['src']);
        $lintFinder = new LintFinder();
        $lintCheckCommand = new LintCommand($bootstrapConfigInitializer, $bootstrapConfigResolver, $lintConsoleOutput, $lintConfig, $lintFinder);
        $lintInitCommand = new LintInitCommand($bootstrapConfigInitializer);

        // Create Application instance
        $application = new LintApplication(
            $lintCheckCommand,
            $lintInitCommand,
        );
        $appInit = $application->initRun();
        $appInit->setAutoExit(false);

        // Create ApplicationTester
        $tester = new ApplicationTester($appInit);

        // Simulate running the application
        $statusCode = $tester->run([]);

        // Assertions
        $this->assertEquals(Command::SUCCESS, $statusCode);

        // Prepare the ConsoleOutput for reading
        rewind($consoleOutput->getStream());
        $display = stream_get_contents($consoleOutput->getStream());
        $display = str_replace(\PHP_EOL, "\n", $display);

        $this->assertStringContainsString('PHPLint', $display);
        $this->assertStringContainsString('Finished', $display);
    }

    public function testRunExceptionally()
    {
        // Mock Exception
        $exceptionMock = new Exception('Test exception');

        // Create ConsoleOutput instance
        $consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));

        // Simulate running the application with an exception
        $statusCode = LintApplication::runExceptionally($exceptionMock, $consoleOutput);

        // Prepare the ConsoleOutput for reading
        rewind($consoleOutput->getStream());
        $display = stream_get_contents($consoleOutput->getStream());
        $display = str_replace(\PHP_EOL, "\n", $display);

        // Assertions
        $this->assertEquals(Command::FAILURE, $statusCode);
        $this->assertStringContainsString('PHPLint', $display);
        $this->assertStringContainsString('Test exception', $display);
    }
}
