<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Resolver\Config;

use PHPLint\Config\LintConfig;
use PHPLint\Resolver\Config\LintSkipPathsResolver;
use PHPUnit\Framework\TestCase;

class LintSkipPathsResolverTest extends TestCase
{
    public function testResolve()
    {
        $lintConfig = new LintConfig();
        $skipPathsResolver = new LintSkipPathsResolver();

        // Test case 1: No skip paths
        $this->assertEquals([], $skipPathsResolver->resolve($lintConfig));

        // Test case 2: Skip existing class
        $lintConfig->skip([LintConfig::class]);
        $this->assertEquals([], $skipPathsResolver->resolve($lintConfig));

        // Test case 3: One skip path
        $lintConfig->skip(['tests/Main/Config']);
        $this->assertEquals([getcwd() . '/tests/Main/Config'], $skipPathsResolver->resolve($lintConfig));

        // Test case 4: Multiple skip paths
        $lintConfig->skip(['tests/Main/Config', 'tests/Main/Console']);
        $this->assertEquals([
            getcwd() . '/tests/Main/Config',
            getcwd() . '/tests/Main/Console',
        ], $skipPathsResolver->resolve($lintConfig));

        // Test case 5: Invalid skip path
        $lintConfig->skip(['/nonexistent/path']);
        $this->assertEquals([], $skipPathsResolver->resolve($lintConfig));

        // Test case 6: Skip path starting with DIRECTORY_SEPARATOR
        $lintConfig->skip([DIRECTORY_SEPARATOR . 'tests/Main/Config']);
        $this->assertEquals([getcwd() . '/tests/Main/Config'], $skipPathsResolver->resolve($lintConfig));

        // Test case 7: One skip path from root directory
        $lintConfig->skip([__DIR__]);
        $this->assertEquals([getcwd() . '/tests/Main/Resolver/Config'], $skipPathsResolver->resolve($lintConfig));
    }
}
