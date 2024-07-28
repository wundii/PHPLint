<?php

declare(strict_types=1);

namespace Main\Console;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Wundii\PHPLint\Config\LintConfig;
use Wundii\PHPLint\Console\Output\LintSymfonyStyle;
use Wundii\PHPLint\Process\LintProcessResult;
use Wundii\PHPLint\Process\StatusEnum;

class LintConsoleOutputTest extends TestCase
{
    public function testLintProcessResultToConsoleWithOk()
    {
        $filename = __DIR__ . '/fileNotExists.php';
        $lintProcessResult = new LintProcessResult(
            StatusEnum::OK,
            $filename,
            'Some lint result',
            10,
        );

        $expected = <<<EOT
#1 - line 10 [{$filename}]
Ok: Some lint result


EOT;

        $this->lintProcessResultToConsole($lintProcessResult, $expected);
    }

    public function testLintProcessResultToConsoleWithNotice()
    {
        $filename = __DIR__ . '/Files/File1.php';
        $lintProcessResult = new LintProcessResult(
            StatusEnum::NOTICE,
            $filename,
            'Some lint result',
            1,
        );

        $expected = <<<EOT
#1 - line 1 [{$filename}]
Notice: Some lint result
00001| <?php
00002| 
00003| declare(strict_types=1);
00004| 
00005| echo 'Hello, world!';


EOT;

        $this->lintProcessResultToConsole($lintProcessResult, $expected);
    }

    public function testLintProcessResultToConsoleWithWarning()
    {
        $filename = __DIR__ . '/Files/File1.php';
        $lintProcessResult = new LintProcessResult(
            StatusEnum::WARNING,
            $filename,
            'Some lint result',
            2,
        );

        $expected = <<<EOT
#1 - line 2 [{$filename}]
Warning: Some lint result
00001| <?php
00002| 
00003| declare(strict_types=1);
00004| 
00005| echo 'Hello, world!';
00006| 


EOT;

        $this->lintProcessResultToConsole($lintProcessResult, $expected);
    }

    public function testLintProcessResultToConsoleWithError()
    {
        $filename = __DIR__ . '/Files/File1.php';
        $lintProcessResult = new LintProcessResult(
            StatusEnum::ERROR,
            $filename,
            'Some lint result',
            46,
        );

        $expected = <<<EOT
#1 - line 46 [{$filename}]
Error: Some lint result
00042| 
00043| \$person = new Person('Bob');
00044| 
00045| echo \$person->getName();
00046| 


EOT;

        $this->lintProcessResultToConsole($lintProcessResult, $expected);
    }

    public function testLintProcessResultToConsoleWithRunning()
    {
        $filename = __DIR__ . '/Files/File1.php';
        $lintProcessResult = new LintProcessResult(
            StatusEnum::RUNNING,
            $filename,
            'Some lint result',
            10,
        );

        $expected = <<<EOT
#1 - line 10 [{$filename}]
Running: Some lint result
00006| 
00007| \$variable = 42;
00008| 
00009| if (\$variable > 30) {
00010|     echo 'Variable is greater than 30.';
00011| } else {
00012|     echo 'Variable is not greater than 30.';
00013| }
00014| 


EOT;

        $this->lintProcessResultToConsole($lintProcessResult, $expected);
    }

    public function lintProcessResultToConsole(LintProcessResult $lintProcessResult, string $expected): void
    {
        $lintConfig = new LintConfig();
        $consoleInput = new ArgvInput();
        $consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));

        $lintConsoleOutput = new LintSymfonyStyle($lintConfig, $consoleInput, $consoleOutput);
        $lintConsoleOutput->messageByProcessResult($lintProcessResult);

        rewind($consoleOutput->getStream());
        $display = stream_get_contents($consoleOutput->getStream());
        $display = str_replace(\PHP_EOL, "\n", $display);

        $this->assertEquals($expected, $display);
    }
}
