<?php

declare(strict_types=1);

namespace PHPLint\Lint;

use PHPLint\Cache\LintCache;
use PHPLint\Config\LintConfig;
use PHPLint\Console\Output\LintConsoleOutput;
use PHPLint\Finder\LintFinder;
use PHPLint\Process\LintProcessResult;
use PHPLint\Process\LintProcessTask;
use PHPLint\Process\StatusEnum;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Process\Process;

final class Lint
{
    private readonly LintCache $lintCache;

    public function __construct(
        private readonly LintConsoleOutput $lintConsoleOutput,
        private readonly LintConfig $lintConfig,
        private readonly LintFinder $lintFinder,
    ) {
        $adapter = new NullAdapter();

        if ($this->lintConfig->isCacheActivated()) {
            $adapter = new FilesystemAdapter('cache', 0, $this->lintConfig->getCacheDirectory());
        }

        $this->lintCache = new LintCache($adapter);
    }

    public function run(): void
    {
        $processes = [];
        $processResults = [];
        $count = $this->lintFinder->count();
        $iterator = $this->lintFinder->getIterator();
        $asyncProcess = $this->lintConfig->getAsyncProcess();

        $this->lintConsoleOutput->progressBarStart($count);

        while ($iterator->valid() || $processes !== []) {
            for ($i = count($processes); $iterator->valid() && $i < $asyncProcess; ++$i) {
                $currentFile = $iterator->current();
                $filename = $currentFile->getRealPath();

                if (! $this->lintCache->isMd5FileValid($filename)) {
                    $lintProcess = $this->createLintProcess($filename);
                    $lintProcess->start();

                    $this->lintConsoleOutput->progressBarAdvance();

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

        $this->lintConsoleOutput->progressBarFinish();

        krsort($processResults);
        foreach ($processResults as $processResult) {
            $this->processResultToConsole($processResult);
        }
    }

    public function createLintProcess(string $filename): Process
    {
        $command = [PHP_BINARY];

        if (PHP_SAPI !== 'cli') {
            $command = [PHP_BINARY . DIRECTORY_SEPARATOR . 'php'];
        }

        $command[] = '-d display_errors=1';
        $command[] = '-d error_reporting=E_ALL';
        $command[] = '-d memory_limit=' . $this->lintConfig->getMemoryLimit();
        $command[] = '-n';
        $command[] = '-l';
        $command[] = $filename;

        return new Process($command);
    }

    public function processResultToConsole(LintProcessResult $lintProcessResult): void
    {
        if ($lintProcessResult->getStatus() === StatusEnum::OK) {
            return;
        }

        $this->lintConsoleOutput->messageByProcessResult($lintProcessResult);
    }
}
