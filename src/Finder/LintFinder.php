<?php

declare(strict_types=1);

namespace PHPLint\Finder;

use ArrayIterator;
use Iterator;
use LogicException;
use PHPLint\Config\LintConfig;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class LintFinder extends Finder
{
    public function getFilesFromLintConfig(LintConfig $lintConfig): self
    {
        $excludes = $lintConfig->getSkipPath();

        foreach ($lintConfig->getPaths() as $path) {
            $path = realpath($path);

            if ($path === false) {
                continue;
            }

            if (is_dir($path)) {
                $this->append($this->getFinderFromPath($path, $excludes));
            } elseif (is_file($path)) {
                $this->append([new SplFileInfo($path, $path, $path)]);
            }
        }

        return $this;
    }

    /**
     * @param array<string> $excludes
     */
    public function getFinderFromPath(string $path, array $excludes = []): Finder
    {
        $finder = new Finder();

        $path = realpath($path);
        if ($path === false) {
            return $finder;
        }

        $finder->files();
        $finder->name('*.php');
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
