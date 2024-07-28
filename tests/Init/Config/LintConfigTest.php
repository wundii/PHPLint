<?php

declare(strict_types=1);

namespace Init\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Wundii\PHPLint\Config\LintConfig;
use Wundii\PHPLint\Config\OptionEnum;

class LintConfigTest extends TestCase
{
    public function testGetDefaultPathsDefault()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals([], $lintConfig->getArrayWithStrings(OptionEnum::PATHS));
    }

    public function testSetPaths()
    {
        $lintConfig = new LintConfig();
        $lintConfig->paths(['path/to/dir1', 'path/to/dir2']);

        $this->assertEquals(['path/to/dir1', 'path/to/dir2'], $lintConfig->getArrayWithStrings(OptionEnum::PATHS));
    }

    public function testGetDefaultSkip()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals([], $lintConfig->getArrayWithStrings(OptionEnum::SKIP));
    }

    public function testSetSkip()
    {
        $lintConfig = new LintConfig();
        $lintConfig->skip(['className', 'file1.php', 'file2.php']);

        $this->assertEquals(['className', 'file1.php', 'file2.php'], $lintConfig->getArrayWithStrings(OptionEnum::SKIP));
    }

    public function testGetDefaultAsyncProcess()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals(10, $lintConfig->getInteger(OptionEnum::ASYNC_PROCESS));
    }

    public function testSetAsyncProcess()
    {
        $lintConfig = new LintConfig();
        $lintConfig->asyncProcess(5);

        $this->assertEquals(5, $lintConfig->getInteger(OptionEnum::ASYNC_PROCESS));
    }

    public function testGetDefaultAsyncProcessTimeout()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals(10, $lintConfig->getInteger(OptionEnum::ASYNC_PROCESS));
    }

    public function testSetAsyncProcessTimeout()
    {
        $lintConfig = new LintConfig();
        $lintConfig->asyncProcessTimeout(120);

        $this->assertEquals(120, $lintConfig->getInteger(OptionEnum::ASYNC_PROCESS_TIMEOUT));
    }

    public function testGetDefaultConsoleNotice()
    {
        $lintConfig = new LintConfig();

        $this->assertTrue($lintConfig->getBoolean(OptionEnum::CONSOLE_NOTICE));
    }

    public function testDisableConsoleNotice()
    {
        $lintConfig = new LintConfig();
        $lintConfig->disableConsoleNotice();

        $this->assertFalse($lintConfig->getBoolean(OptionEnum::CONSOLE_NOTICE));
    }

    public function testGetDefaultConsoleWarning()
    {
        $lintConfig = new LintConfig();

        $this->assertTrue($lintConfig->getBoolean(OptionEnum::CONSOLE_WARNING));
    }

    public function testDisableConsoleWarning()
    {
        $lintConfig = new LintConfig();
        $lintConfig->disableWarning();

        $this->assertFalse($lintConfig->getBoolean(OptionEnum::CONSOLE_WARNING));
    }

    public function testGetDefaultNoExitCode()
    {
        $lintConfig = new LintConfig();

        $this->assertFalse($lintConfig->getBoolean(OptionEnum::NO_EXIT_CODE));
    }

    public function testSetNoExitCode()
    {
        $lintConfig = new LintConfig();
        $lintConfig->disableExitCode();

        $this->assertTrue($lintConfig->getBoolean(OptionEnum::NO_EXIT_CODE));
    }

    public function testGetDefaultNoProcessBar()
    {
        $lintConfig = new LintConfig();

        $this->assertFalse($lintConfig->getBoolean(OptionEnum::NO_PROGRESS_BAR));
    }

    public function testSetNoProcessBar()
    {
        $lintConfig = new LintConfig();
        $lintConfig->disableProcessBar();

        $this->assertTrue($lintConfig->getBoolean(OptionEnum::NO_PROGRESS_BAR));
    }

    public function testGetDefaultCache()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals(FilesystemAdapter::class, $lintConfig->getString(OptionEnum::CACHE_CLASS));
    }

    public function testSetCacheSuccess()
    {
        $lintConfig = new LintConfig();
        $lintConfig->cacheClass(NullAdapter::class);

        $this->assertEquals(NullAdapter::class, $lintConfig->getString(OptionEnum::CACHE_CLASS));
    }

    public function testSetCacheFail()
    {
        $lintConfig = new LintConfig();
        $lintConfig->cacheClass(ArrayAdapter::class);

        $this->assertEquals(NullAdapter::class, $lintConfig->getString(OptionEnum::CACHE_CLASS));
    }

    public function testGetDefaultCacheDirectory()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals('.phplint', $lintConfig->getString(OptionEnum::CACHE_DIR));
    }

    public function testSetCacheDirectory()
    {
        $lintConfig = new LintConfig();
        $lintConfig->cacheDirectory(__DIR__ . '/path/to/cache/folder');

        $this->assertEquals(__DIR__ . '/path/to/cache/folder', $lintConfig->getString(OptionEnum::CACHE_DIR));
    }

    public function testGetDefaultMemoryLimit()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals('512M', $lintConfig->getString(OptionEnum::MEMORY_LIMIT));
    }

    public function testSetMemoryLimit()
    {
        $lintConfig = new LintConfig();
        $lintConfig->memoryLimit('1G');

        $this->assertEquals('1G', $lintConfig->getString(OptionEnum::MEMORY_LIMIT));
    }

    public function testGetDefaultPhpCgiExecutable()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals('php', $lintConfig->getString(OptionEnum::PHP_CGI_EXECUTABLE));
    }

    public function testSetPhpCgiExecutable()
    {
        $lintConfig = new LintConfig();
        $lintConfig->phpCgiExecutable('php.exe');

        $this->assertEquals('php.exe', $lintConfig->getString(OptionEnum::PHP_CGI_EXECUTABLE));
    }

    public function testGetDefaultPhpExtension()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals('php', $lintConfig->getString(OptionEnum::PHP_EXTENSION));
    }

    public function testSetPhpExtension()
    {
        $lintConfig = new LintConfig();
        $lintConfig->phpExtension('php8');

        $this->assertEquals('php8', $lintConfig->getString(OptionEnum::PHP_EXTENSION));
    }
}
