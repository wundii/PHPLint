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

    public function testIsAllowWarning()
    {
        $lintConfig = new LintConfig();

        $this->assertTrue($lintConfig->isAllowWarning());
    }

    public function testSetAllowWarning()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setAllowWarning(false);

        $this->assertFalse($lintConfig->isAllowWarning());
    }

    public function testIsAllowNotice()
    {
        $lintConfig = new LintConfig();

        $this->assertTrue($lintConfig->isAllowNotice());
    }

    public function testSetAllowNotice()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setAllowNotice(false);

        $this->assertFalse($lintConfig->isAllowNotice());
    }

    public function testIsIgnoreExitCode()
    {
        $lintConfig = new LintConfig();

        $this->assertFalse($lintConfig->isIgnoreExitCode());
    }

    public function testSetIgnoreExitCode()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setIgnoreExitCode(true);

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
        $lintConfig->setIgnoreProcessBar(true);

        $this->assertTrue($lintConfig->isIgnoreProcessBar());
    }

    public function testIsCache()
    {
        $lintConfig = new LintConfig();

        $this->assertTrue($lintConfig->isCache());
    }

    public function testSetCache()
    {
        $lintConfig = new LintConfig();
        $lintConfig->setCache(false);

        $this->assertFalse($lintConfig->isCache());
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
