<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Config;

use PHPLint\Config\LintConfig;
use PHPLint\Config\OptionEnum;
use PHPUnit\Framework\TestCase;

class LintConfigTest extends TestCase
{
    public function testGetPathsDefault()
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

    public function testGetSkip()
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

    public function testGetAsyncProcess()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals(10, $lintConfig->getAsyncProcess());
    }

    public function testSetAsyncProcess()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setAsyncProcess(5);

        $this->assertEquals(5, $lintConfig->getAsyncProcess());
    }

    public function testIsEnableWarning()
    {
        $lintConfig = new LintConfig();

        $this->assertTrue($lintConfig->isEnableWarning());
    }

    public function testDisableWarning()
    {
        $lintConfig = new LintConfig();
        $lintConfig->disableWarning();

        $this->assertFalse($lintConfig->isEnableWarning());
    }

    public function testIsEnableNotice()
    {
        $lintConfig = new LintConfig();

        $this->assertTrue($lintConfig->isEnableNotice());
    }

    public function testDisableNotice()
    {
        $lintConfig = new LintConfig();
        $lintConfig->disableNotice();

        $this->assertFalse($lintConfig->isEnableNotice());
    }

    public function testIsIgnoreExitCode()
    {
        $lintConfig = new LintConfig();

        $this->assertFalse($lintConfig->isIgnoreExitCode());
    }

    public function testSetIgnoreExitCode()
    {
        $lintConfig = new LintConfig();
        $lintConfig->ignoreExitCode();

        $this->assertTrue($lintConfig->isIgnoreExitCode());
    }

    public function testIsIgnoreProcessBar()
    {
        $lintConfig = new LintConfig();

        $this->assertFalse($lintConfig->isIgnoreProcessBar());
    }

    public function testSetIgnoreProcessBar()
    {
        $lintConfig = new LintConfig();
        $lintConfig->ignoreProcessBar();

        $this->assertTrue($lintConfig->isIgnoreProcessBar());
    }

    public function testIsCacheActivated()
    {
        $lintConfig = new LintConfig();

        $this->assertTrue($lintConfig->isCacheActivated());
    }

    public function testSetCache()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setCache(false);

        $this->assertFalse($lintConfig->isCacheActivated());
    }

    public function testGetCacheDirectory()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals('.phplint', $lintConfig->getCacheDirectory());
    }

    public function testSetCacheDirectory()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setCacheDirectory(__DIR__ . '/path/to/cache/folder');

        $this->assertEquals(__DIR__ . '/path/to/cache/folder', $lintConfig->getCacheDirectory());
    }
}
