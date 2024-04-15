<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Resolver\Config;

use PHPLint\Config\LintConfig;
use PHPLint\Resolver\Config\LintPathsResolver;
use PHPUnit\Framework\TestCase;

class LintPathsResolverTest extends TestCase
{
    public function testResolve()
    {
        $lintConfig = new LintConfig();
        $lintPathsResolver = new LintPathsResolver();

        $this->assertEquals([getcwd() . DIRECTORY_SEPARATOR], $lintPathsResolver->resolve($lintConfig));

        $lintConfig->paths(['nonexistent/path', 'nonexistent/file.php']);
        $this->assertEquals([], $lintPathsResolver->resolve($lintConfig));

        $lintConfig->paths([__DIR__, __FILE__, 'nonexistent/path']);
        $this->assertEquals([__DIR__, __FILE__], $lintPathsResolver->resolve($lintConfig));
    }
}
