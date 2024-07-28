<?php

declare(strict_types=1);

namespace Wundii\PHPLint\Finder;

use ArrayIterator;
use Iterator;
use LogicException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Wundii\PHPLint\Config\LintConfig;
use Wundii\PHPLint\Config\OptionEnum;
use Wundii\PHPLint\Resolver\Config\LintPathsResolver;
use Wundii\PHPLint\Resolver\Config\LintSkipPathsResolver;

final class LintFinder extends Finder
{
    public function __construct(
        private readonly LintSkipPathsResolver $lintSkipPathsResolver,
        private readonly LintPathsResolver $lintPathsResolver,
    ) {
        parent::__construct();
    }

    public function getFilesFromLintConfig(LintConfig $lintConfig): self
    {
        $excludes = $this->lintSkipPathsResolver->resolve($lintConfig);
        $extension = $lintConfig->getString(OptionEnum::PHP_CGI_EXECUTABLE);

        foreach ($this->lintPathsResolver->resolve($lintConfig) as $path) {
            if (! is_dir($path) && ! is_file($path)) {
                continue;
            }

            if (is_dir($path)) {
                $this->append($this->getFinderFromPath($extension, $path, $excludes));
                continue;
            }

            if (is_file($path)) {
                $this->append([new SplFileInfo($path, $path, $path)]);
            }
        }

        return $this;
    }

    /**
     * @param string[] $excludes
     */
    public function getFinderFromPath(string $extension, string $path, array $excludes = []): Finder
    {
        $finder = new Finder();

        $path = realpath($path);
        if ($path === false) {
            return $finder;
        }

        $finder->files();
        $finder->name('*.' . $extension);
        $finder->in($path);

        foreach ($excludes as $exclude) {
            if (! str_starts_with($exclude, $path)) {
                continue;
            }

            $finder->exclude(substr($exclude, strlen($path) + 1));
        }

        return $finder;
    }

    public function count(): int
    {
        try {
            return parent::count();
        } catch (LogicException) {
            return 0;
        }
    }

    public function getIterator(): Iterator
    {
        try {
            return parent::getIterator();
        } catch (LogicException) {
            return new ArrayIterator();
        }
    }
}
