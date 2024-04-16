<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Process;

use PHPLint\Config\LintConfig;
use PHPLint\Process\LintProcessResult;
use PHPLint\Process\LintProcessTask;
use PHPLint\Process\StatusEnum;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

class LintProcessTaskTest extends TestCase
{
    public function testRegexError()
    {
        $errorMessages = [
            'Parse error: syntax error, unexpected T_STRING in /path/to/file.php line 10',
            'PHP Fatal error: Call to undefined function foo() in /path/to/file.php line 20',
            'Fatal error: Cannot redeclare some_function() (previously declared in /path/to/file.php:15) in /path/to/file.php on line 25',
            'PHP Parse error: syntax error, unexpected \'{\' in /path/to/file.php on line 30',
        ];

        $expectedMessages = [
            'syntax error, unexpected T_STRING',
            'Call to undefined function foo()',
            'Cannot redeclare some_function() (previously declared', // TODO: fix this with "in /path/to/file.php:15)"
            'syntax error, unexpected \'{\'',
        ];

        $expectedLines = [
            '10',
            '20',
            '25',
            '30',
        ];

        foreach ($errorMessages as $kex => $errorMessage) {
            $matched = preg_match(LintProcessTask::REGEX_ERROR, $errorMessage, $matches);

            $this->assertSame(1, $matched, $errorMessage);
            $this->assertArrayHasKey('error', $matches);
            $this->assertArrayHasKey('line', $matches);
            $this->assertNotEmpty($matches['error']);
            $this->assertNotEmpty($matches['line']);
            $this->assertSame($expectedMessages[$kex], trim($matches['error']));
            $this->assertSame($expectedLines[$kex], $matches['line']);
        }
    }

    public function testRegexWarning()
    {
        $warningMessages = [
            'Warning: Undefined variable $foo in /path/to/file.php on line 5',
            'PHP Warning: Use of undefined constant BAR - assumed \'BAR\' (this will throw an Error in a future version of PHP) in /path/to/file.php line 15',
            'Deprecated: Function some_function() is deprecated in /path/to/file.php line 25',
            'Warning: Invalid argument supplied for foreach() in /path/to/file.php on line 30',
        ];

        $expectedMessages = [
            'Undefined variable $foo',
            'Use of undefined constant BAR - assumed \'BAR\' (this will throw an Error', // TODO: fix this with "in a future version of PHP)"
            'Function some_function() is deprecated',
            'Invalid argument supplied for foreach()',
        ];

        $expectedLines = [
            '5',
            '15',
            '25',
            '30',
        ];

        foreach ($warningMessages as $key => $warningMessage) {
            $matched = preg_match(LintProcessTask::REGEX_WARNING, $warningMessage, $matches);

            $this->assertSame(1, $matched);
            $this->assertArrayHasKey('error', $matches);
            $this->assertArrayHasKey('line', $matches);
            $this->assertNotEmpty($matches['error']);
            $this->assertNotEmpty($matches['line']);
            $this->assertSame($expectedMessages[$key], trim($matches['error']));
            $this->assertSame($expectedLines[$key], $matches['line']);
        }
    }

    /**
     * @throws Exception
     */
    public function testGetProcessResult()
    {
        $lintConfig = new LintConfig();
        $processMock = $this->createMock(Process::class);
        $processMock->method('getOutput')->willReturn('No syntax errors detected');
        $splFileInfoMock = $this->createMock(SplFileInfo::class);
        $splFileInfoMock->method('getRealPath')->willReturn('/path/to/file.php');

        $lintProcessTask = new LintProcessTask($lintConfig, $processMock, $splFileInfoMock);
        $result = $lintProcessTask->getProcessResult();

        $this->assertInstanceOf(LintProcessResult::class, $result);
        $this->assertSame(StatusEnum::OK->name, $result->getStatus()->name);
        $this->assertSame('/path/to/file.php', $result->getFilename());
        $this->assertSame('', $result->getResult());
        $this->assertNull($result->getLine());
    }

    /**
     * @throws Exception
     */
    public function testGetProcessResultWithError()
    {
        $lintConfig = new LintConfig();
        $processMock = $this->createMock(Process::class);
        $processMock->method('getOutput')->willReturn('Parse error: Invalid syntax in /path/to/file.php line 10');
        $splFileInfoMock = $this->createMock(SplFileInfo::class);
        $splFileInfoMock->method('getRealPath')->willReturn('/path/to/file.php');

        $lintProcessTask = new LintProcessTask($lintConfig, $processMock, $splFileInfoMock);

        $result = $lintProcessTask->getProcessResult();

        $this->assertInstanceOf(LintProcessResult::class, $result);
        $this->assertSame(StatusEnum::ERROR->name, $result->getStatus()->name);
        $this->assertSame('/path/to/file.php', $result->getFilename());
        $this->assertSame('Invalid syntax', $result->getResult());
        $this->assertSame(10, $result->getLine());
    }

    /**
     * @throws Exception
     */
    public function testGetProcessResultWithWarningAllow()
    {
        $lintConfig = new LintConfig();
        $processMock = $this->createMock(Process::class);
        $processMock->method('getOutput')->willReturn('Warning: Undefined variable $foo in /path/to/file.php line 5');
        $splFileInfoMock = $this->createMock(SplFileInfo::class);
        $splFileInfoMock->method('getRealPath')->willReturn('/path/to/file.php');

        $lintProcessTask = new LintProcessTask($lintConfig, $processMock, $splFileInfoMock);
        $result = $lintProcessTask->getProcessResult();

        $this->assertInstanceOf(LintProcessResult::class, $result);
        $this->assertSame(StatusEnum::WARNING->name, $result->getStatus()->name);
        $this->assertSame('/path/to/file.php', $result->getFilename());
        $this->assertSame('Undefined variable $foo', $result->getResult());
        $this->assertSame(5, $result->getLine());
    }

    /**
     * @throws Exception
     */
    public function testGetProcessResultWithWarningDisallow()
    {
        $lintConfig = new LintConfig();
        $lintConfig->disableWarning();
        $processMock = $this->createMock(Process::class);
        $processMock->method('getOutput')->willReturn('Warning: Undefined variable $foo in /path/to/file.php line 5');
        $splFileInfoMock = $this->createMock(SplFileInfo::class);
        $splFileInfoMock->method('getRealPath')->willReturn('/path/to/file.php');

        $lintProcessTask = new LintProcessTask($lintConfig, $processMock, $splFileInfoMock);
        $result = $lintProcessTask->getProcessResult();

        $this->assertInstanceOf(LintProcessResult::class, $result);
        $this->assertSame(StatusEnum::OK->name, $result->getStatus()->name);
        $this->assertSame('/path/to/file.php', $result->getFilename());
        $this->assertSame('', $result->getResult());
        $this->assertNull($result->getLine());
    }

    /**
     * @throws Exception
     */
    public function testGetProcessResultWithNoticeAndAllow()
    {
        $lintConfig = new LintConfig();
        $processMock = $this->createMock(Process::class);
        $processMock->method('getOutput')->willReturn('Notice: Undefined variable $foo in /path/to/file.php line 5');
        $splFileInfoMock = $this->createMock(SplFileInfo::class);
        $splFileInfoMock->method('getRealPath')->willReturn('/path/to/file.php');

        $lintProcessTask = new LintProcessTask($lintConfig, $processMock, $splFileInfoMock);
        $result = $lintProcessTask->getProcessResult();

        $this->assertInstanceOf(LintProcessResult::class, $result);
        $this->assertSame(StatusEnum::NOTICE->name, $result->getStatus()->name);
        $this->assertSame('/path/to/file.php', $result->getFilename());
        $this->assertSame('Undefined variable $foo', $result->getResult());
        $this->assertSame(5, $result->getLine());
    }

    /**
     * @throws Exception
     */
    public function testGetProcessResultWithNoticeAndDisallow()
    {
        $lintConfig = new LintConfig();
        $lintConfig->disableConsoleNotice();
        $processMock = $this->createMock(Process::class);
        $processMock->method('getOutput')->willReturn('Notice: Undefined variable $foo in /path/to/file.php line 5');
        $splFileInfoMock = $this->createMock(SplFileInfo::class);
        $splFileInfoMock->method('getRealPath')->willReturn('/path/to/file.php');

        $lintProcessTask = new LintProcessTask($lintConfig, $processMock, $splFileInfoMock);
        $result = $lintProcessTask->getProcessResult();

        $this->assertInstanceOf(LintProcessResult::class, $result);
        $this->assertSame(StatusEnum::OK->name, $result->getStatus()->name);
        $this->assertSame('/path/to/file.php', $result->getFilename());
        $this->assertSame('', $result->getResult());
        $this->assertNull($result->getLine());
    }

    /**
     * @throws Exception
     */
    public function testGetProcessResultWithRunning()
    {
        $lintConfig = new LintConfig();
        $processMock = $this->createMock(Process::class);
        $processMock->method('isRunning')->willReturn(true);
        $splFileInfoMock = $this->createMock(SplFileInfo::class);
        $splFileInfoMock->method('getRealPath')->willReturn('/path/to/file.php');

        $lintProcessTask = new LintProcessTask($lintConfig, $processMock, $splFileInfoMock);
        $result = $lintProcessTask->getProcessResult();

        $this->assertInstanceOf(LintProcessResult::class, $result);
        $this->assertSame(StatusEnum::RUNNING->name, $result->getStatus()->name);
        $this->assertSame('/path/to/file.php', $result->getFilename());
        $this->assertSame('Process is still running', $result->getResult());
        $this->assertNull($result->getLine());
    }
}
