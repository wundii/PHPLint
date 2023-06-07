<?php

declare(strict_types=1);

namespace PHPLint\Lint;

use PHPLint\Config\LintConfig;
use PHPLint\Finder\LintFinder;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

final class Lint
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly LintConfig $lintConfig,
        private readonly LintFinder $lintFinder,
    ) {
    }

    public function run(): void
    {
        $this->symfonyStyle->writeln('Linting files...');

        $pid = 0;
        $processes = [];
        $iterator = $this->lintFinder->getIterator();
        $maxAsyncProcess = $this->lintConfig->getAsyncProcess();

        while ($iterator->valid() || $processes !== []) {
            for ($i = count($processes); $iterator->valid() && $i < $maxAsyncProcess; ++$i) {
                $currentFile = $iterator->current();
                $filename = $currentFile->getRealPath();

                $lintProcess = $this->createLintProcess($filename);
                $lintProcess->start();

                ++$pid;
                $processes[$pid] = [
                    'process' => $lintProcess,
                    'file' => $currentFile,
                ];

                $iterator->next();
            }

            foreach ($processes as $runningPid => $runningProcess) {
                $lintProcess = $runningProcess['process'];
                if ($lintProcess->isRunning()) {
                    continue;
                }

                $output = trim($lintProcess->getOutput());
                dump($output);

                unset($processes[$runningPid]);
            }
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
}
