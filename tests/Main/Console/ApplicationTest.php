<?php

declare(strict_types=1);

use PHPLint\Config\LintConfig;
use PHPLint\Console\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\ApplicationTester;

class ApplicationTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testRun()
    {
        // Create Application instance
        $application = new Application(new LintConfig());
        $appInit = $application->initRun();
        $appInit->setAutoExit(false);

        // Create ApplicationTester
        $tester = new ApplicationTester($appInit);

        // Simulate running the application
        $statusCode = $tester->run([]);

        // Assertions
        $this->assertEquals(Command::SUCCESS, $statusCode);
        $this->assertStringContainsString('PHPLint', $tester->getDisplay(true));
    }

    public function testRunExceptionally()
    {
        // Mock Exception
        $exceptionMock = new Exception('Test exception');

        // Create Application instance
        $application = new Application(new LintConfig());

        // Create ConsoleOutput instance
        $consoleOutput = new StreamOutput(fopen('php://memory', 'w', false));

        // Simulate running the application with an exception
        $statusCode = $application->runExceptionally($exceptionMock, $consoleOutput);

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
