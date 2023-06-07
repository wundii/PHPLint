<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Config;

use Exception;
use PHPLint\Config\LintConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LintConfigTest extends TestCase
{
    public function getMockContainerBuilder(): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register(LintConfig::class, LintConfig::class)
            ->setArgument('$containerBuilder', $containerBuilder);

        return $containerBuilder;
    }

    /**
     * @throws Exception
     */
    public function testGetPhpCgiExecutable()
    {
        $lintConfig = new LintConfig($this->getMockContainerBuilder());

        $this->assertEquals('php', $lintConfig->getPhpCgiExecutable());
        $this->assertInstanceOf(LintConfig::class, $lintConfig->getService(LintConfig::class));
    }

    public function testSetPhpCgiExecutable()
    {
        $lintConfig = new LintConfig($this->getMockContainerBuilder());
        $lintConfig->setPhpCgiExecutable('php7');

        $this->assertEquals('php7', $lintConfig->getPhpCgiExecutable());
    }

    public function testGetPathsDefault()
    {
        $lintConfig = new LintConfig($this->getMockContainerBuilder());

        $this->assertEquals([getcwd() . DIRECTORY_SEPARATOR], $lintConfig->getPaths());
    }

    public function testSetPaths()
    {
        $lintConfig = new LintConfig($this->getMockContainerBuilder());
        $lintConfig->setPaths(['path/to/dir1', 'path/to/dir2']);

        $this->assertEquals(['path/to/dir1', 'path/to/dir2'], $lintConfig->getPaths());
    }

    public function testGetSkip()
    {
        $lintConfig = new LintConfig($this->getMockContainerBuilder());

        $this->assertEquals([], $lintConfig->getSkip());
    }

    public function testSetSkip()
    {
        $lintConfig = new LintConfig($this->getMockContainerBuilder());
        $lintConfig->setSkip(['className', 'file1.php', 'file2.php']);

        $this->assertEquals(['className', 'file1.php', 'file2.php'], $lintConfig->getSkip());
    }

    public function testGetSkipPath()
    {
        $containerBuilder = $this->getMockContainerBuilder();
        $lintConfig = new LintConfig($containerBuilder);

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
        $lintConfig = new LintConfig($this->getMockContainerBuilder());

        $this->assertEquals([], $lintConfig->getSets());
    }

    public function testSetSets()
    {
        $lintConfig = new LintConfig($this->getMockContainerBuilder());
        $lintConfig->setSets(['set1', 'set2']);

        $this->assertEquals(['set1', 'set2'], $lintConfig->getSets());
    }
}
