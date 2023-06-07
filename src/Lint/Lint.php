<?php

declare(strict_types=1);

namespace PHPLint\Lint;

use PHPLint\Config\LintConfig;
use PHPLint\Console\Output\LintConsoleOutput;
use PHPLint\Finder\LintFinder;
use PHPLint\Process\LintProcessEntity;
use PHPLint\Process\LintProcessResult;
use PHPLint\Process\StatusEnum;
use Symfony\Component\Process\Process;

final class Lint
{
    public function __construct(
        private readonly LintConsoleOutput $lintConsoleOutput,
        private readonly LintConfig $lintConfig,
        private readonly LintFinder $lintFinder,
    ) {
    }

    public function run(): void
    {
        $processes = [];
        $count = $this->lintFinder->count();
        $iterator = $this->lintFinder->getIterator();
        $asyncProcess = $this->lintConfig->getAsyncProcess();

        $this->lintConsoleOutput->progressBarStart($count);

        while ($iterator->valid() || $processes !== []) {
            for ($i = count($processes); $iterator->valid() && $i < $asyncProcess; ++$i) {
                $currentFile = $iterator->current();
                $filename = $currentFile->getRealPath();

                $lintProcess = $this->createLintProcess($filename);
                $lintProcess->start();

                $this->lintConsoleOutput->progressBarAdvance();

                $processes[] = new LintProcessEntity($lintProcess, $currentFile);

                $iterator->next();
            }

            foreach ($processes as $pid => $runningProcess) {
                /** @var LintProcessEntity $runningProcess */
                if ($runningProcess->isRunning()) {
                    continue;
                }

                $processOutput = $runningProcess->getProcessOutput();
                $this->processResultToConsole($processOutput);

                unset($processes[$pid]);
            }
        }

        $this->lintConsoleOutput->progressBarFinish();
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
