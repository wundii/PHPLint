<?php

declare(strict_types=1);

namespace PHPLint\Config;

final class LintConfig
{
    private string $phpCgiExecutable = 'php';

    /**
     * @var array<string>
     */
    private array $paths = [];

    /**
     * @var array<string>
     */
    private array $skip = [];

    /**
     * @var array<string>
     */
    private array $sets = [];

    private string $memoryLimit = '512M';

    private int $asyncProcess = 10;

    private bool $enableWarning = true;

    private bool $enableNotice = true;

    private bool $ignoreExitCode = false;

    private bool $ignoreProcessBar = false;

    private bool $cache = true;

    private string $cacheDirectory = '.phplint';

    public function getPhpCgiExecutable(): string
    {
        return $this->phpCgiExecutable;
    }

    public function setPhpCgiExecutable(string $string): void
    {
        $this->phpCgiExecutable = $string;
    }

    /**
     * @return array<string>
     */
    public function getPaths(): array
    {
        if ($this->paths === []) {
            return [getcwd() . DIRECTORY_SEPARATOR];
        }

        return $this->paths;
    }

    /**
     * @param array<string> $paths
     */
    public function setPaths(array $paths): void
    {
        $this->paths = $paths;
    }

    /**
     * @return array<string>
     */
    public function getSkip(): array
    {
        return $this->skip;
    }

    /**
     * @return array<string>
     */
    public function getSkipPath(): array
    {
        $paths = [];

        foreach ($this->skip as $path) {
            if (class_exists($path)) {
                continue;
            }

            if (! str_starts_with($path, (string) getcwd())) {
                if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
                    $path = substr($path, 1);
                }

                $path = getcwd() . DIRECTORY_SEPARATOR . $path;
            }

            if (! is_dir($path)) {
                continue;
            }

            $realPath = realpath($path);

            if ($realPath !== false) {
                $paths[] = $realPath;
            }
        }

        return $paths;
    }

    /**
     * @param array<string> $skip
     */
    public function setSkip(array $skip): void
    {
        $this->skip = $skip;
    }

    /**
     * @return array<string>
     */
    public function getSets(): array
    {
        return $this->sets;
    }

    /**
     * @param array<string> $sets
     */
    public function setSets(array $sets): void
    {
        $this->sets = $sets;
    }

    public function getMemoryLimit(): string
    {
        return $this->memoryLimit;
    }

    public function setMemoryLimit(string $memoryLimit): void
    {
        $this->memoryLimit = $memoryLimit;
    }

    public function getAsyncProcess(): int
    {
        return $this->asyncProcess;
    }

    public function setAsyncProcess(int $asyncProcess): void
    {
        $this->asyncProcess = $asyncProcess;
    }

    public function isEnableWarning(): bool
    {
        return $this->enableWarning;
    }

    public function disableWarning(): void
    {
        $this->enableWarning = false;
    }

    public function isEnableNotice(): bool
    {
        return $this->enableNotice;
    }

    public function disableNotice(): void
    {
        $this->enableNotice = false;
    }

    public function isIgnoreExitCode(): bool
    {
        return $this->ignoreExitCode;
    }

    public function ignoreExitCode(): void
    {
        $this->ignoreExitCode = true;
    }

    public function isIgnoreProcessBar(): bool
    {
        return $this->ignoreProcessBar;
    }

    public function ignoreProcessBar(): void
    {
        $this->ignoreProcessBar = true;
    }

    public function isCacheActivated(): bool
    {
        return $this->cache;
    }

    public function setCache(bool $cache): void
    {
        $this->cache = $cache;
    }

    public function getCacheDirectory(): string
    {
        return $this->cacheDirectory;
    }

    public function setCacheDirectory(string $cacheDirectory): void
    {
        $this->cacheDirectory = $cacheDirectory;
    }
}
