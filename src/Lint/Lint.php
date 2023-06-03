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

        $this->lintFinder->count();
        //
        // $iterator = $this->lintFinder->getIterator();
        // dump($iterator->valid());
        //
        // foreach ($iterator as $file) {
        //     dump($file->getRealPath());
        //     dump($iterator->valid());
        // }
        // dump($iterator->valid());
        //
        //
        // $process = $this->createLintProcess('testFile.php');
        // dump('start process');
        // $process->start();
        // dump('is running');
        // $process->isRunning();
        // dump('output');
        // sleep(1);
        // dump($process->getOutput());
        // dump('end process');
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
