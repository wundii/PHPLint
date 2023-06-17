<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Console;

use PHPLint\Config\LintConfig;
use PHPLint\Console\Output\LintConsoleOutput;
use PHPLint\Finder\LintFinder;
use PHPLint\Lint\Lint;
use PHPLint\Process\LintProcessResult;
use PHPLint\Process\StatusEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class LintConsoleOutputTest extends TestCase
{
    public function testLintProcessResultToConsoleWithOk()
    {
        $lintProcessResult = new LintProcessResult(
            StatusEnum::OK,
            __DIR__ . '/fileNotExists.php',
            'Some lint result',
            10,
        );

        $expected = <<<EOT
#1 - line 10 [/var/www/phplint/tests/Main/Console/fileNotExists.php]
Ok: Some lint result


EOT;

        $this->lintProcessResultToConsole($lintProcessResult, $expected);
    }

    public function testLintProcessResultToConsoleWithNotice()
    {
        $lintProcessResult = new LintProcessResult(
            StatusEnum::NOTICE,
            __DIR__ . '/Files/File1.php',
            'Some lint result',
            1,
        );

        $expected = <<<EOT
#1 - line 1 [/var/www/phplint/tests/Main/Console/Files/File1.php]
Notice: Some lint result
00001| <?php
00002| 
00003| declare(strict_types=1);
00004| 
00005| echo "Hello, world!";


EOT;

        $this->lintProcessResultToConsole($lintProcessResult, $expected);
    }

    public function testLintProcessResultToConsoleWithWarning()
    {
        $lintProcessResult = new LintProcessResult(
            StatusEnum::WARNING,
            __DIR__ . '/Files/File1.php',
            'Some lint result',
            2,
        );

        $expected = <<<EOT
#1 - line 2 [/var/www/phplint/tests/Main/Console/Files/File1.php]
Warning: Some lint result
00001| <?php
00002| 
00003| declare(strict_types=1);
00004| 
00005| echo "Hello, world!";
00006| 


EOT;

        $this->lintProcessResultToConsole($lintProcessResult, $expected);
    }

    public function testLintProcessResultToConsoleWithError()
    {
        $lintProcessResult = new LintProcessResult(
            StatusEnum::ERROR,
            __DIR__ . '/Files/File1.php',
            'Some lint result',
            41,
        );

        $expected = <<<EOT
#1 - line 41 [/var/www/phplint/tests/Main/Console/Files/File1.php]
Error: Some lint result
00037| }
00038| 
00039| \$person = new Person("Bob");
00040| 
00041| echo \$person->getName();


EOT;

        $this->lintProcessResultToConsole($lintProcessResult, $expected);
    }

    public function testLintProcessResultToConsoleWithRunning()
    {
        $lintProcessResult = new LintProcessResult(
            StatusEnum::RUNNING,
            __DIR__ . '/Files/File1.php',
            'Some lint result',
            10,
        );

        $expected = <<<EOT
#1 - line 10 [/var/www/phplint/tests/Main/Console/Files/File1.php]
Running: Some lint result
00006| 
00007| \$variable = 42;
00008| 
00009| if (\$variable > 30) {
00010|     echo "Variable is greater than 30.";
00011| } else {
00012|     echo "Variable is not greater than 30.";
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
        $symfonyStyle = new SymfonyStyle($consoleInput, $consoleOutput);

        $lintConsoleOutput = new LintConsoleOutput($symfonyStyle, $lintConfig);
        $lintConsoleOutput->messageByProcessResult($lintProcessResult);

        rewind($consoleOutput->getStream());
        $display = stream_get_contents($consoleOutput->getStream());
        $display = str_replace(\PHP_EOL, "\n", $display);

        $this->assertEquals($expected, $display);
    }
}
