<?php

declare(strict_types=1);

namespace PHPLint\Tests\E2E\Console;

use PHPLint\Bootstrap\BootstrapConfigInitializer;
use PHPLint\Bootstrap\BootstrapConfigResolver;
use PHPLint\Config\LintConfig;
use PHPLint\Console\Commands\LintCommand;
use PHPLint\Console\LintApplication;
use PHPLint\Console\Output\LintSymfonyStyle;
use PHPLint\Finder\LintFinder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class LintCommandTest extends TestCase
{
    private StreamOutput $consoleOutput;

    public function createLintCommand(LintConfig $lintConfig): CommandTester
    {
        $consoleInput = new ArgvInput();
        $this->consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));
        $lintConsoleOutput = new LintSymfonyStyle($lintConfig, $consoleInput, $this->consoleOutput);
        $bootstrapConfigInitializer = new BootstrapConfigInitializer(new Filesystem(), $lintConsoleOutput);
        $bootstrapConfigResolver = new BootstrapConfigResolver();

        $lintCommand = new LintCommand(
            $bootstrapConfigInitializer,
            $bootstrapConfigResolver,
            $lintConfig,
            new LintFinder(),
        );

        return new CommandTester($lintCommand);
    }

    public function testEndToEndSuccess()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setPaths(['src']);

        $lintCommand = $this->createLintCommand($lintConfig);

        $this->assertSame(0, $lintCommand->execute([]));
    }

    public function testEndToEndFirstDisplayLine()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setPaths(['src']);

        $lintCommand = $this->createLintCommand($lintConfig);
        $lintCommand->execute([]);

        $display = $lintCommand->getDisplay(true);
        $firstDisplayLine = explode("\n", $display)[0];

        preg_match('/>\s(.)/', $firstDisplayLine, $matches);

        $this->assertCount(2, $matches, 'First line should contain the command (' . $firstDisplayLine . ')');
        $this->assertStringContainsString('PHPLint ' . LintApplication::VERSION . ' - current PHP version: ' . PHP_VERSION, $display);
    }

    public function testEndToEndFail()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setPaths(['tests/FaultyFiles']);

        $lintCommand = $this->createLintCommand($lintConfig);

        $this->assertSame(1, $lintCommand->execute([]));
    }

    /**
     * @depends testEndToEndFail
     */
    public function testEndToEndFailWithSuccessReturn()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setPaths(['tests/FaultyFiles']);
        $lintConfig->ignoreExitCode();

        $lintCommand = $this->createLintCommand($lintConfig);

        $this->assertSame(0, $lintCommand->execute([]));
    }
}
