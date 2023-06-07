<?php

declare(strict_types=1);

namespace PHPLint\Console\Output;

use PHPLint\Process\LintProcessResult;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Style\SymfonyStyle;

final class LintConsoleOutput
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle
    ) {
    }

    public function startApplication(string $version): void
    {
        $argv = $_SERVER['argv'] ?? [];

        $this->symfonyStyle->writeln('> ' . implode('', $argv));
        $this->symfonyStyle->writeln('<fg=blue;options=bold>PHP</><fg=yellow;options=bold>Lint</> ' . $version);
        $this->symfonyStyle->writeln('');
    }

    public function finishApplication(string $executionTime): void
    {
        $usageMemory = Helper::formatMemory(memory_get_usage(true));

        $this->symfonyStyle->writeln(sprintf('Memory usage: %s', $usageMemory));
        $this->symfonyStyle->success(sprintf('Finished in %s seconds', $executionTime));
    }

    public function progressBarStart(int $count): void
    {
        $this->symfonyStyle->writeln('Linting files...');
        $this->symfonyStyle->newLine();

        $this->symfonyStyle->progressStart($count);
    }

    public function progressBarAdvance(): void
    {
        $this->symfonyStyle->progressAdvance();
    }

    public function progressBarFinish(): void
    {
        $this->symfonyStyle->progressFinish();
    }

    public function messageByProcessResult(LintProcessResult $lintProcessResult): void
    {
        $this->symfonyStyle->writeln($lintProcessResult->getFilename());
        $this->symfonyStyle->writeln($lintProcessResult->getResult());
        $this->symfonyStyle->writeln((string) $lintProcessResult->getLine());
        $this->symfonyStyle->newLine();
    }
}
