<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Console;

use Exception;
use PHPLint\Bootstrap\BootstrapConfigInitializer;
use PHPLint\Bootstrap\BootstrapConfigResolver;
use PHPLint\Bootstrap\BootstrapInputResolver;
use PHPLint\Config\LintConfig;
use PHPLint\Console\Commands\LintCommand;
use PHPLint\Console\Commands\LintInitCommand;
use PHPLint\Console\LintApplication;
use PHPLint\Console\Output\LintSymfonyStyle;
use PHPLint\Finder\LintFinder;
use PHPLint\Resolver\Config\LintPathsResolver;
use PHPLint\Resolver\Config\LintSkipPathsResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;

class LintApplicationTest extends TestCase
{
    public function getMockContainerBuilder(): ContainerBuilder
    {
        return new ContainerBuilder();
    }

    public function testRun()
    {
        $lintConfig = new LintConfig();
        $consoleInput = new ArgvInput();
        $consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));
        $lintConsoleOutput = new LintSymfonyStyle($lintConfig, $consoleInput, $consoleOutput);
        $bootstrapConfigInitializer = new BootstrapConfigInitializer(new Filesystem(), $lintConsoleOutput);
        $bootstrapInputResolver = new BootstrapInputResolver($consoleInput);
        $bootstrapConfigResolver = new BootstrapConfigResolver($bootstrapInputResolver);
        $lintConfig->paths(['src']);
        $lintFinder = new LintFinder(
            new LintSkipPathsResolver(),
            new LintPathsResolver(),
        );
        $lintCommand = new LintCommand(
            $bootstrapConfigInitializer,
            $bootstrapConfigResolver,
            $bootstrapInputResolver,
            $lintConfig,
            $lintFinder
        );
        $lintInitCommand = new LintInitCommand($bootstrapConfigInitializer);

        // Create Application instance
        $application = new LintApplication(
            $lintCommand,
            $lintInitCommand,
        );
        $application->setAutoExit(false);

        // Create ApplicationTester
        $tester = new ApplicationTester($application);

        // Simulate running the application
        $statusCode = $tester->run([]);

        // Assertions
        $this->assertEquals(Command::SUCCESS, $statusCode);

        // Prepare the ConsoleOutput for reading
        $display = $tester->getDisplay(true);

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
