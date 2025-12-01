<?php

declare(strict_types=1);

namespace Main\Console;

use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Wundii\PHPLint\Bootstrap\BootstrapConfigInitializer;
use Wundii\PHPLint\Bootstrap\BootstrapConfigResolver;
use Wundii\PHPLint\Bootstrap\BootstrapInputResolver;
use Wundii\PHPLint\Config\LintConfig;
use Wundii\PHPLint\Console\Commands\LintCommand;
use Wundii\PHPLint\Console\Commands\LintInitCommand;
use Wundii\PHPLint\Console\LintApplication;
use Wundii\PHPLint\Console\Output\LintSymfonyStyle;
use Wundii\PHPLint\Finder\LintFinder;
use Wundii\PHPLint\Resolver\Config\LintPathsResolver;
use Wundii\PHPLint\Resolver\Config\LintSkipPathsResolver;

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

        // $this->assertStringContainsString('PHPLint', $display);
        // $this->assertStringContainsString('Finished', $display);
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
