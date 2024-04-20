<?php

declare(strict_types=1);

namespace PHPLint\Config;

use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Webmozart\Assert\Assert;

final class LintConfig extends LintConfigParameter
{
    public const DEFAULT_CACHE_CLASS = NullAdapter::class;

    public function __construct()
    {
        $this->setParameter(OptionEnum::ASYNC_PROCESS, 10);
        $this->setParameter(OptionEnum::ASYNC_PROCESS_TIMEOUT, 60);
        $this->setParameter(OptionEnum::CACHE_CLASS, FilesystemAdapter::class);
        $this->setParameter(OptionEnum::CACHE_DIR, '.phplint');
        $this->setParameter(OptionEnum::CONSOLE_NOTICE, true);
        $this->setParameter(OptionEnum::CONSOLE_WARNING, true);
        $this->setParameter(OptionEnum::MEMORY_LIMIT, '512M');
        $this->setParameter(OptionEnum::NO_EXIT_CODE, false);
        $this->setParameter(OptionEnum::NO_PROGRESS_BAR, false);
        $this->setParameter(OptionEnum::PHP_CGI_EXECUTABLE, 'php');
    }

    public function asyncProcess(int $asyncProcess): void
    {
        $this->setParameter(OptionEnum::ASYNC_PROCESS, $asyncProcess);
    }

    public function asyncProcessTimeout(int $asyncProcessTimeout): void
    {
        $this->setParameter(OptionEnum::ASYNC_PROCESS_TIMEOUT, $asyncProcessTimeout);
    }

    public function cacheClass(string $cacheClass): void
    {
        /**
         * In the first step, we only allowed the AbstractAdapter class
         */
        if (is_a($cacheClass, AbstractAdapter::class, true)) {
            $this->setParameter(OptionEnum::CACHE_CLASS, $cacheClass);
            return;
        }

        $this->setParameter(OptionEnum::CACHE_CLASS, self::DEFAULT_CACHE_CLASS);
    }

    public function cacheDirectory(string $cacheDirectory): void
    {
        $this->setParameter(OptionEnum::CACHE_DIR, $cacheDirectory);
    }

    public function disableConsoleNotice(): void
    {
        $this->setParameter(OptionEnum::CONSOLE_NOTICE, false);
    }

    public function disableWarning(): void
    {
        $this->setParameter(OptionEnum::CONSOLE_WARNING, false);
    }

    public function disableExitCode(): void
    {
        $this->setParameter(OptionEnum::NO_EXIT_CODE, true);
    }

    public function disableProcessBar(): void
    {
        $this->setParameter(OptionEnum::NO_PROGRESS_BAR, true);
    }

    public function memoryLimit(string $memoryLimit): void
    {
        $this->setParameter(OptionEnum::MEMORY_LIMIT, $memoryLimit);
    }

    public function phpCgiExecutable(string $string): void
    {
        $this->setParameter(OptionEnum::PHP_CGI_EXECUTABLE, $string);
    }

    /**
     * @param string[] $paths
     */
    public function paths(array $paths): void
    {
        Assert::allString($paths);

        $this->setParameter(OptionEnum::PATHS, $paths);
    }

    /**
     * @param string[] $skip
     */
    public function skip(array $skip): void
    {
        Assert::allString($skip);

        $this->setParameter(OptionEnum::SKIP, $skip);
    }
}
