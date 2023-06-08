<?php

declare(strict_types=1);

namespace PHPLint\Console\Output;

use PHPLint\Process\LintProcessResult;
use PHPLint\Process\StatusEnum;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Style\SymfonyStyle;

final class LintConsoleOutput
{
    /**
     * @var int
     */
    private const SNIPPED_LINE = 5;

    /**
     * @var int
     */
    private const LINE_LENGTH = 5;

    private bool $isSuccess = true;

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

    public function finishApplication(string $executionTime): bool
    {
        $usageMemory = Helper::formatMemory(memory_get_usage(true));

        $this->symfonyStyle->writeln(sprintf('Memory usage: %s', $usageMemory));

        if (! $this->isSuccess) {
            $this->symfonyStyle->error(sprintf('Finished in %s', $executionTime));
            return true;
        }

        $this->symfonyStyle->success(sprintf('Finished in %s', $executionTime));
        return false; // false means success
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
        $messageColor = match ($lintProcessResult->getStatus()) {
            StatusEnum::OK => 'green',
            StatusEnum::ERROR => 'red',
            StatusEnum::WARNING => 'yellow',
            default => 'red',
        };

        $line01 = sprintf('<options=bold>line %s </><fg=gray;options=bold>[%s]</>', $lintProcessResult->getLine(), $lintProcessResult->getFilename());
        $line02 = sprintf('<fg=bright-%s>%s</>: <fg=%s>%s</>', $messageColor, ucfirst($lintProcessResult->getStatus()->value), $messageColor, $lintProcessResult->getResult());

        $this->symfonyStyle->writeln($line01);
        $this->symfonyStyle->writeln($line02);
        $this->loadCodeSnippet($lintProcessResult->getFilename(), (int) $lintProcessResult->getLine(), $messageColor);
        $this->symfonyStyle->newLine();

        $this->isSuccess = false;
    }

    private function loadCodeSnippet(string $filename, int $line, string $messageColor): void
    {
        $lineStart = $line - self::SNIPPED_LINE;
        $lineEnd = $line + (self::SNIPPED_LINE - 1);

        $content = file_get_contents($filename);
        if ($content === false) {
            return;
        }

        $contentArray = explode("\n", $content);

        $lineCnt = 0;
        foreach ($contentArray as $contentLine) {
            if ($lineCnt >= $lineStart && $lineCnt < $lineEnd) {
                $lineNumberPost = $lineCnt + 1;
                $tmp = str_pad((string) $lineNumberPost, self::LINE_LENGTH, '0', STR_PAD_LEFT);
                $lineNumberPre = substr($tmp, 0, self::LINE_LENGTH - strlen((string) $lineNumberPost));

                if ($lineCnt + 1 === $line) {
                    $result = sprintf('<fg=bright-%s;options=bold>%s</><fg=%s>%s</><fg=blue;options=bold>:</> <fg=%s>%s</>', $messageColor, $lineNumberPre, $messageColor, $lineNumberPost, $messageColor, $contentLine);
                } else {
                    $result = sprintf('<fg=gray;options=bold>%s</><fg=white>%s</><fg=blue;options=bold>:</> <fg=white>%s</>', $lineNumberPre, $lineNumberPost, $contentLine);
                }

                $this->symfonyStyle->writeln($result);
            }

            ++$lineCnt;
        }
    }
}
