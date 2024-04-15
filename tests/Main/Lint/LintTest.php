<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Lint;

use PHPLint\Config\LintConfig;
use PHPLint\Console\Output\LintSymfonyStyle;
use PHPLint\Finder\LintFinder;
use PHPLint\Lint\Lint;
use PHPLint\Process\LintProcessResult;
use PHPLint\Process\StatusEnum;
use PHPLint\Resolver\Config\LintPathsResolver;
use PHPLint\Resolver\Config\LintSkipPathsResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Process\Process;

class LintTest extends TestCase
{
    public function testCreateLintProcess()
    {
        $lintConfig = new LintConfig();
        $consoleInput = new ArgvInput();
        $consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));
        $lintConsoleOutput = new LintSymfonyStyle($lintConfig, $consoleInput, $consoleOutput);
        $lintConfig->setMemoryLimit('256M');

        $lint = new Lint(
            $lintConsoleOutput,
            $lintConfig,
            new LintFinder(
                new LintSkipPathsResolver(),
                new LintPathsResolver(),
            ),
        );

        $filename = 'path/to/file.php';
        $process = $lint->createLintProcess($filename);

        $this->assertInstanceOf(Process::class, $process);

        $phpBinary = PHP_BINARY;
        $expectedCommand = sprintf("'%s' '-d display_errors=1' '-d error_reporting=E_ALL' '-d memory_limit=256M' '-n' '-l' 'path/to/file.php'", $phpBinary);

        $this->assertEquals($expectedCommand, $process->getCommandLine());
    }

    public function testProcessResultToConsoleOutputIsEmpty()
    {
        $lintProcessResult = new LintProcessResult(
            StatusEnum::OK,
            __DIR__ . '/example.php',
            'Some lint result',
            10,
        );

        $this->processResultToConsole($lintProcessResult, true);
    }

    public function testProcessResultToConsoleOutputIsNotEmpty()
    {
        $lintProcessResult = new LintProcessResult(
            StatusEnum::NOTICE,
            __DIR__ . '/Files/File1.php',
            'Some lint result',
            1,
        );

        $this->processResultToConsole($lintProcessResult, false);
    }

    public function processResultToConsole(LintProcessResult $lintProcessResult, bool $assertEmpty): void
    {
        $lintConfig = new LintConfig();
        $consoleInput = new ArgvInput();
        $consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));
        $lintConsoleOutput = new LintSymfonyStyle($lintConfig, $consoleInput, $consoleOutput);

        $lint = new Lint(
            $lintConsoleOutput,
            $lintConfig,
            new LintFinder(
                new LintSkipPathsResolver(),
                new LintPathsResolver(),
            ),
        );
        $lint->processResultToConsole($lintProcessResult);

        rewind($consoleOutput->getStream());
        $display = stream_get_contents($consoleOutput->getStream());
        $display = str_replace(\PHP_EOL, "\n", $display);

        if ($assertEmpty) {
            $this->assertEmpty($display);
        } else {
            $this->assertNotEmpty($display);
        }
    }
}
