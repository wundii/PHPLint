<?php

declare(strict_types=1);

namespace E2E\Console;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Wundii\PHPLint\Bootstrap\BootstrapConfigInitializer;
use Wundii\PHPLint\Bootstrap\BootstrapConfigResolver;
use Wundii\PHPLint\Bootstrap\BootstrapInputResolver;
use Wundii\PHPLint\Config\LintConfig;
use Wundii\PHPLint\Console\Commands\LintCommand;
use Wundii\PHPLint\Console\LintApplication;
use Wundii\PHPLint\Console\Output\LintSymfonyStyle;
use Wundii\PHPLint\Finder\LintFinder;
use Wundii\PHPLint\Resolver\Config\LintPathsResolver;
use Wundii\PHPLint\Resolver\Config\LintSkipPathsResolver;

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
                    ...$argvInput,
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

    #[Depends('testEndToEndFail')]
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
            '--no-progress-bar',
        ]);
        $execute = $lintCommand->execute([]);
        $display = $lintCommand->getDisplay(true);

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

        $this->assertSame(1, $execute);
        $this->assertStringNotContainsString('3/3 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%', $display);
    }
}
