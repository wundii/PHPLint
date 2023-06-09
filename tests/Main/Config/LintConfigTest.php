<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Config;

use PHPLint\Config\LintConfig;
use PHPUnit\Framework\TestCase;

class LintConfigTest extends TestCase
{
    public function testSetPhpCgiExecutable()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setPhpCgiExecutable('php7');

        $this->assertEquals('php7', $lintConfig->getPhpCgiExecutable());
    }

    public function testGetPathsDefault()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals([getcwd() . DIRECTORY_SEPARATOR], $lintConfig->getPaths());
    }

    public function testSetPaths()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setPaths(['path/to/dir1', 'path/to/dir2']);

        $this->assertEquals(['path/to/dir1', 'path/to/dir2'], $lintConfig->getPaths());
    }

    public function testGetSkip()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals([], $lintConfig->getSkip());
    }

    public function testSetSkip()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setSkip(['className', 'file1.php', 'file2.php']);

        $this->assertEquals(['className', 'file1.php', 'file2.php'], $lintConfig->getSkip());
    }

    public function testGetSkipPath()
    {
        $lintConfig = new LintConfig();

        // Test case 1: No skip paths
        $this->assertEquals([], $lintConfig->getSkipPath());

        // Test case 2: Skip existing class
        $lintConfig->setSkip([LintConfig::class]);
        $this->assertEquals([], $lintConfig->getSkipPath());

        // Test case 3: One skip path
        $lintConfig->setSkip(['tests/Main/Config']);
        $this->assertEquals([getcwd() . '/tests/Main/Config'], $lintConfig->getSkipPath());

        // Test case 4: Multiple skip paths
        $lintConfig->setSkip(['tests/Main/Config', 'tests/Main/Console']);
        $this->assertEquals([
            getcwd() . '/tests/Main/Config',
            getcwd() . '/tests/Main/Console',
        ], $lintConfig->getSkipPath());

        // Test case 5: Invalid skip path
        $lintConfig->setSkip(['/nonexistent/path']);
        $this->assertEquals([], $lintConfig->getSkipPath());

        // Test case 6: Skip path starting with DIRECTORY_SEPARATOR
        $lintConfig->setSkip([DIRECTORY_SEPARATOR . 'tests/Main/Config']);
        $this->assertEquals([getcwd() . '/tests/Main/Config'], $lintConfig->getSkipPath());

        // Test case 7: One skip path from root directory
        $lintConfig->setSkip([__DIR__]);
        $this->assertEquals([getcwd() . '/tests/Main/Config'], $lintConfig->getSkipPath());
    }

    public function testGetSets()
    {
        $lintConfig = new LintConfig();

        $this->assertEquals([], $lintConfig->getSets());
    }

    public function testSetSets()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setSets(['set1', 'set2']);

        $this->assertEquals(['set1', 'set2'], $lintConfig->getSets());
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
