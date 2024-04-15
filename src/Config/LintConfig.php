<?php

declare(strict_types=1);

namespace PHPLint\Config;

use Webmozart\Assert\Assert;

final class LintConfig extends LintConfigParameter
{
    private string $memoryLimit = '512M';

    private int $asyncProcess = 10;

    private bool $enableWarning = true;

    private bool $enableNotice = true;

    private bool $ignoreExitCode = false;

    private bool $ignoreProcessBar = false;

    private bool $cache = true;

    private string $cacheDirectory = '.phplint';

    public function __construct()
    {
        $this->setParameter(OptionEnum::ASYNC_PROCESS, 10);
        $this->setParameter(OptionEnum::CACHE, true);
        $this->setParameter(OptionEnum::CACHE_DIRECTORY, '.phplint');
        $this->setParameter(OptionEnum::CONSOLE_NOTICE, true);
        $this->setParameter(OptionEnum::CONSOLE_WARNING, true);
        $this->setParameter(OptionEnum::MEMORY_LIMIT, '512M');
        $this->setParameter(OptionEnum::NO_EXIT_CODE, false);
        $this->setParameter(OptionEnum::NO_PROGRESS_BAR, false);
        $this->setParameter(OptionEnum::PHP_CGI_EXECUTABLE, 'php');
    }

    public function phpCgiExecutable(string $string): void
    {
        $this->setParameter(OptionEnum::PHP_CGI_EXECUTABLE, $string);
    }

    /**
     * @param array<string> $paths
     */
    public function paths(array $paths): void
    {
        Assert::allString($paths);

        $this->setParameter(OptionEnum::PATHS, $paths);
    }

    /**
     * @param array<string> $skip
     */
    public function skip(array $skip): void
    {
        Assert::allString($skip);

        $this->setParameter(OptionEnum::SKIP, $skip);
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
