<?php

declare(strict_types=1);

namespace Wundii\PHPLint\Lint;

use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Process\Process;
use Wundii\PHPLint\Cache\LintCache;
use Wundii\PHPLint\Config\LintConfig;
use Wundii\PHPLint\Config\OptionEnum;
use Wundii\PHPLint\Console\Output\LintSymfonyStyle;
use Wundii\PHPLint\Finder\LintFinder;
use Wundii\PHPLint\Process\LintProcessResult;
use Wundii\PHPLint\Process\LintProcessTask;
use Wundii\PHPLint\Process\StatusEnum;

final class Lint
{
    private readonly LintCache $lintCache;

    public function __construct(
        private readonly LintSymfonyStyle $lintSymfonyStyle,
        private readonly LintConfig $lintConfig,
        private readonly LintFinder $lintFinder,
    ) {
        $adapter = new (LintConfig::DEFAULT_CACHE_CLASS)();
        $cacheClass = $this->lintConfig->getString(OptionEnum::CACHE_CLASS);
        $cacheDir = $this->lintConfig->getString(OptionEnum::CACHE_DIR);

        if (is_a($cacheClass, AbstractAdapter::class, true)) {
            $adapter = new $cacheClass('cache', 0, $cacheDir);
        }

        $this->lintCache = new LintCache($adapter);
    }

    public function run(): void
    {
        $processes = [];
        $processResults = [];
        $count = $this->lintFinder->count();
        $iterator = $this->lintFinder->getIterator();
        $asyncProcess = $this->lintConfig->getInteger(OptionEnum::ASYNC_PROCESS);
        $asyncProcessTimeout = $this->lintConfig->getInteger(OptionEnum::ASYNC_PROCESS_TIMEOUT);

        $this->lintSymfonyStyle->progressBarStart($count);

        while ($iterator->valid() || $processes !== []) {
            for ($i = count($processes); $iterator->valid() && $i < $asyncProcess; ++$i) {
                $currentFile = $iterator->current();
                $filename = $currentFile->getRealPath();

                if (! $this->lintCache->isMd5FileValid($filename)) {
                    $lintProcess = $this->createLintProcess($filename, $asyncProcessTimeout);
                    $lintProcess->start();

                    $this->lintSymfonyStyle->progressBarAdvance();

                    $processes[] = new LintProcessTask($this->lintConfig, $lintProcess, $currentFile);
                }

                $iterator->next();
            }

            foreach ($processes as $pid => $runningProcess) {
                /** @var LintProcessTask $runningProcess */
                if ($runningProcess->isRunning()) {
                    continue;
                }

                $processResult = $runningProcess->getProcessResult();
                $processResults[] = $processResult;

                unset($processes[$pid]);

                if ($processResult->getStatus() === StatusEnum::OK) {
                    $this->lintCache->setMd5File($processResult->getFilename());
                }
            }
        }

        $this->lintSymfonyStyle->progressBarFinish();

        krsort($processResults);
        foreach ($processResults as $processResult) {
            $this->processResultToConsole($processResult);
        }
    }

    public function createLintProcess(string $filename, int $timeout): Process
    {
        $command = [PHP_BINARY];

        if (PHP_SAPI !== 'cli') {
            $command = [PHP_BINARY . DIRECTORY_SEPARATOR . $this->lintConfig->getString(OptionEnum::PHP_CGI_EXECUTABLE)];
        }

        $command[] = '-d display_errors=1';
        $command[] = '-d error_reporting=E_ALL';
        $command[] = '-d memory_limit=' . $this->lintConfig->getString(OptionEnum::MEMORY_LIMIT);
        $command[] = '-n';
        $command[] = '-l';
        $command[] = $filename;

        return new Process($command, timeout: $timeout);
    }

    public function processResultToConsole(LintProcessResult $lintProcessResult): void
    {
        if ($lintProcessResult->getStatus() === StatusEnum::OK) {
            return;
        }

        $this->lintSymfonyStyle->messageByProcessResult($lintProcessResult);
    }
}
