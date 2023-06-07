<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Finder;

use Iterator;
use PHPLint\Config\LintConfig;
use PHPLint\Finder\LintFinder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class LintFinderTest extends TestCase
{
    public function testGetFilesFromLintConfigWithDirectory()
    {
        $lintConfig = new LintConfig(new ContainerBuilder());
        $lintConfig->setPaths([__DIR__ . '/Files']);

        $lintFinder = new LintFinder();
        $result = $lintFinder->getFilesFromLintConfig($lintConfig);

        // Assert that the result is an instance of LintFinder
        $this->assertInstanceOf(LintFinder::class, $result);

        // Assert that the count of files in the LintFinder is correct
        $this->assertEquals(3, $result->count());
    }

    public function testGetFilesFromLintConfigWithInvalidPath()
    {
        $lintConfig = new LintConfig(new ContainerBuilder());
        $lintConfig->setPaths(['/path/to/invalid']);
        $lintFinder = new LintFinder();
        $result = $lintFinder->getFilesFromLintConfig($lintConfig);

        // Assert that the result is an instance of LintFinder
        $this->assertInstanceOf(LintFinder::class, $result);

        // Assert that the count of files in the LintFinder is correct
        $this->assertEquals(0, $result->count());
    }

    public function testGetFinderFromPath()
    {
        $lintFinder = new LintFinder();
        $result = $lintFinder->getFinderFromPath(__DIR__ . '/Files');

        // Assert that the result is an instance of Finder
        $this->assertInstanceOf(Finder::class, $result);

        // Assert that the Finder is configured correctly
        $this->assertTrue($result->hasResults());
    }

    public function testCountGreaterThan0()
    {
        $file = __DIR__ . '/Files/File3.php';
        $lintFinder = new LintFinder();
        $lintFinder->append([new SplFileInfo($file, $file, $file)]);
        $count = $lintFinder->count();

        // Assert that the count is correct
        $this->assertEquals(1, $count);
    }

    public function testCountEqual0()
    {
        $lintFinder = new LintFinder();
        $count = $lintFinder->count();

        // Assert that the count is correct
        $this->assertEquals(0, $count);
    }

    public function testGetIterator()
    {
        $file = __DIR__ . '/Files/File4.php';
        $lintFinder = new LintFinder();
        $lintFinder->append([new SplFileInfo($file, $file, $file)]);
        $iterator = $lintFinder->getIterator();

        // Assert that the iterator is an instance of Iterator
        $this->assertInstanceOf(Iterator::class, $iterator);

        // Assert that the iterator is not empty
        $this->assertTrue($iterator->valid());
    }
}