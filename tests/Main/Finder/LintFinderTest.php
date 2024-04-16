<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Finder;

use Iterator;
use PHPLint\Config\LintConfig;
use PHPLint\Finder\LintFinder;
use PHPLint\Resolver\Config\LintPathsResolver;
use PHPLint\Resolver\Config\LintSkipPathsResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class LintFinderTest extends TestCase
{
    public function testGetFilesFromLintConfigWithDirectory()
    {
        $lintConfig = new LintConfig();
        $lintConfig->paths([__DIR__ . '/Files']);

        $lintFinder = new LintFinder(
            new LintSkipPathsResolver(),
            new LintPathsResolver(),
        );
        $result = $lintFinder->getFilesFromLintConfig($lintConfig);

        $this->assertInstanceOf(LintFinder::class, $result);
        $this->assertEquals(3, $result->count());
    }

    public function testGetFilesFromLintConfigWithDirectoryAndExcludes()
    {
        $lintConfig = new LintConfig();
        $lintConfig->paths([__DIR__ . '/Files']);
        $lintConfig->skip([__DIR__ . '/Files/Folder']);

        $lintFinder = new LintFinder(
            new LintSkipPathsResolver(),
            new LintPathsResolver(),
        );
        $result = $lintFinder->getFilesFromLintConfig($lintConfig);

        $this->assertInstanceOf(LintFinder::class, $result);
        $this->assertEquals(2, $result->count());
    }

    public function testGetFilesFromLintConfigWithInvalidPath()
    {
        $lintConfig = new LintConfig();
        $lintConfig->paths(['/path/to/invalid']);
        $lintFinder = new LintFinder(
            new LintSkipPathsResolver(),
            new LintPathsResolver(),
        );
        $result = $lintFinder->getFilesFromLintConfig($lintConfig);

        $this->assertInstanceOf(LintFinder::class, $result);
        $this->assertEquals(0, $result->count());
    }

    public function testGetFinderFromPath()
    {
        $lintFinder = new LintFinder(
            new LintSkipPathsResolver(),
            new LintPathsResolver(),
        );
        $result = $lintFinder->getFinderFromPath(__DIR__ . '/Files');

        $this->assertInstanceOf(Finder::class, $result);
        $this->assertTrue($result->hasResults());
    }

    public function testCountByFileCount1()
    {
        $lintConfig = new LintConfig();
        $lintConfig->paths([__DIR__ . '/Files/File3.php']);
        $lintFinder = new LintFinder(
            new LintSkipPathsResolver(),
            new LintPathsResolver(),
        );
        $lintFinder = $lintFinder->getFilesFromLintConfig($lintConfig);
        $count = $lintFinder->count();

        $this->assertEquals(1, $count);
    }

    public function testCountByFileCount0()
    {
        $lintFinder = new LintFinder(
            new LintSkipPathsResolver(),
            new LintPathsResolver(),
        );
        $count = $lintFinder->count();

        $this->assertEquals(0, $count);
    }

    public function testGetIteratorByFileCount1()
    {
        $lintConfig = new LintConfig();
        $lintConfig->paths([__DIR__ . '/Files/File4.php']);
        $lintFinder = new LintFinder(
            new LintSkipPathsResolver(),
            new LintPathsResolver(),
        );
        $lintFinder = $lintFinder->getFilesFromLintConfig($lintConfig);
        $iterator = $lintFinder->getIterator();

        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertTrue($iterator->valid());
    }

    public function testGetIteratorByFileCount0()
    {
        $lintFinder = new LintFinder(
            new LintSkipPathsResolver(),
            new LintPathsResolver(),
        );
        $iterator = $lintFinder->getIterator();

        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertFalse($iterator->valid());
    }
}
