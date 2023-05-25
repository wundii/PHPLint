<?php

declare(strict_types=1);

namespace Main\Config;

use PHPLint\Config\LintConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LintConfigTest extends TestCase
{
    public function getMockContainerBuilder(): ContainerBuilder
    {
        return new ContainerBuilder();
    }

    public function testGetPhpCgiExecutable()
    {
        $lintConfig = new LintConfig($this->getMockContainerBuilder());

        $this->assertEquals('php', $lintConfig->getPhpCgiExecutable());
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
