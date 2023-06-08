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

    private bool $allowWarning = true;

    private bool $allowNotice = true;

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

    public function isAllowWarning(): bool
    {
        return $this->allowWarning;
    }

    public function setAllowWarning(bool $allowWarning): void
    {
        $this->allowWarning = $allowWarning;
    }

    public function isAllowNotice(): bool
    {
        return $this->allowNotice;
    }

    public function setAllowNotice(bool $allowNotice): void
    {
        $this->allowNotice = $allowNotice;
    }
}
