<?php

declare(strict_types=1);

namespace Main\Resolver\Config;

use Wundii\PHPLint\Config\LintConfig;
use Wundii\PHPLint\Resolver\Config\LintPathsResolver;
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
