<?php

declare(strict_types=1);

namespace PHPLint\Tests\E2E\Console;

use PHPLint\Bootstrap\BootstrapConfigInitializer;
use PHPLint\Bootstrap\BootstrapConfigResolver;
use PHPLint\Bootstrap\BootstrapInputResolver;
use PHPLint\Config\LintConfig;
use PHPLint\Console\Commands\LintCommand;
use PHPLint\Console\LintApplication;
use PHPLint\Console\Output\LintSymfonyStyle;
use PHPLint\Finder\LintFinder;
use PHPLint\Resolver\Config\LintPathsResolver;
use PHPLint\Resolver\Config\LintSkipPathsResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class LintCommandTest extends TestCase
{
    private StreamOutput $consoleOutput;

    public function createLintCommand(LintConfig $lintConfig, array $argvInput = []): CommandTester
    {
        if ($argvInput !== []) {
            $first = $argvInput[0] ?? null;
            if ($first !== 'bin/phplint') {
                $argvInput = [
                    'bin/phplint',
                    ...$argvInput
                ];
            }
        }

        $consoleInput = new ArgvInput($argvInput);
        $this->consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));
        $lintConsoleOutput = new LintSymfonyStyle($lintConfig, $consoleInput, $this->consoleOutput);
        $bootstrapConfigInitializer = new BootstrapConfigInitializer(new Filesystem(), $lintConsoleOutput);
        $bootstrapInputResolver = new BootstrapInputResolver($consoleInput);
        $bootstrapConfigResolver = new BootstrapConfigResolver($bootstrapInputResolver);

        $lintCommand = new LintCommand(
            $bootstrapConfigInitializer,
            $bootstrapConfigResolver,
            $bootstrapInputResolver,
            $lintConfig,
            new LintFinder(
                new LintSkipPathsResolver(),
                new LintPathsResolver(),
            ),
        );

        return new CommandTester($lintCommand);
    }

    public function testEndToEndSuccess()
    {
        $lintConfig = new LintConfig();
        $lintConfig->paths(['src']);

        $lintCommand = $this->createLintCommand($lintConfig);

        $this->assertSame(0, $lintCommand->execute([]));
    }

    public function testEndToEndFirstDisplayLine()
    {
        $lintConfig = new LintConfig();
        $lintConfig->paths(['src']);

        $lintCommand = $this->createLintCommand($lintConfig);
        $lintCommand->execute([]);

        $display = $lintCommand->getDisplay(true);
        $firstDisplayLine = explode("\n", $display)[0];

        preg_match('/>\s(.?)/', $firstDisplayLine, $matches);

        $this->assertCount(2, $matches, 'First line should contain the command (' . $firstDisplayLine . ')');
        $this->assertStringContainsString('PHPLint ' . LintApplication::VERSION . ' - current PHP version: ' . PHP_VERSION, $display);
    }

    public function testEndToEndFail()
    {
        $lintConfig = new LintConfig();
        $lintConfig->paths(['tests/FaultyFiles']);

        $lintCommand = $this->createLintCommand($lintConfig);

        $this->assertSame(1, $lintCommand->execute([]));
    }

    /**
     * @depends testEndToEndFail
     */
    public function testEndToEndFailWithSuccessReturn()
    {
        $lintConfig = new LintConfig();
        $lintConfig->paths(['tests/FaultyFiles']);
        $lintConfig->disableExitCode();

        $lintCommand = $this->createLintCommand($lintConfig);

        $this->assertSame(0, $lintCommand->execute([]));
    }

    public function testEndToEndNoConfigWithOptionsFail()
    {
        $lintConfig = new LintConfig();
        $lintConfig->paths(['tests/FaultyFiles']);

        $lintCommand = $this->createLintCommand($lintConfig, [
            '--no-progress-bar'
        ]);
        $execute = $lintCommand->execute([]);
        $display = $lintCommand->getDisplay(true);
        $firstDisplayLine = explode("\n", $display)[0];

        preg_match('/>\s(.?)/', $firstDisplayLine, $matches);

        $this->assertCount(2, $matches, 'First line should contain the command (' . $firstDisplayLine . ')');
        $this->assertSame(1, $execute);
        $this->assertStringContainsString('3/3 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%', $display);
    }

    public function testEndToEndNoConfigWithOptionsSuccess()
    {
        $lintConfig = new LintConfig();
        $lintConfig->paths(['tests/FaultyFiles']);

        $lintCommand = $this->createLintCommand($lintConfig, [
            '--no-config',
            '--no-progress-bar',
        ]);
        $execute = $lintCommand->execute([]);
        $display = $lintCommand->getDisplay(true);
        $firstDisplayLine = explode("\n", $display)[0];

        preg_match('/>\s(.?)/', $firstDisplayLine, $matches);

        $this->assertCount(2, $matches, 'First line should contain the command (' . $firstDisplayLine . ')');
        $this->assertSame(1, $execute);
        $this->assertStringNotContainsString('3/3 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%', $display);
    }
}
