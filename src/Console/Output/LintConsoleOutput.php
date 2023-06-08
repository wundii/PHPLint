<?php

declare(strict_types=1);

namespace PHPLint\Console\Output;

use PHPLint\Console\ConsoleColorEnum;
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

    private int $countFiles = 0;

    public function __construct(
        private readonly SymfonyStyle $symfonyStyle
    ) {
    }

    public function startApplication(string $version): void
    {
        $argv = $_SERVER['argv'] ?? [];

        $message = sprintf(
            '<fg=blue;options=bold>PHP</><fg=yellow;options=bold>Lint</> %s - current PHP version: %s',
            $version,
            PHP_VERSION,
        );
        $this->symfonyStyle->writeln('> ' . implode('', $argv));
        $this->symfonyStyle->writeln($message);
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
        $consoleColorEnum = match ($lintProcessResult->getStatus()) {
            StatusEnum::OK => ConsoleColorEnum::GREEN,
            StatusEnum::NOTICE => ConsoleColorEnum::BLUE,
            StatusEnum::WARNING => ConsoleColorEnum::YELLOW,
            default => ConsoleColorEnum::RED,
        };

        ++$this->countFiles;

        $line01 = sprintf(
            '<fg=white;options=bold>#%d - line %s </><fg=gray;options=bold>[%s]</>',
            $this->countFiles,
            $lintProcessResult->getLine(),
            $lintProcessResult->getFilename(),
        );
        $line02 = sprintf(
            '<fg=%s;options=bold>%s</>: <fg=%s>%s</>',
            $consoleColorEnum->getBrightValue(),
            ucfirst($lintProcessResult->getStatus()->value),
            $consoleColorEnum->value,
            $lintProcessResult->getResult(),
        );

        $this->symfonyStyle->writeln($line01);
        $this->symfonyStyle->writeln($line02);
        $this->loadCodeSnippet($lintProcessResult->getFilename(), (int) $lintProcessResult->getLine(), $consoleColorEnum);
        $this->symfonyStyle->newLine();

        $this->isSuccess = false;
    }

    private function loadCodeSnippet(string $filename, int $line, ConsoleColorEnum $consoleColorEnum): void
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
                    $result = sprintf(
                        '<fg=%s;options=bold>%s</><fg=%s>%s</><fg=blue;options=bold>:</> <fg=%s>%s</>',
                        $consoleColorEnum->getBrightValue(),
                        $lineNumberPre,
                        $consoleColorEnum->value,
                        $lineNumberPost,
                        $consoleColorEnum->value,
                        $contentLine,
                    );
                } else {
                    $result = sprintf(
                        '<fg=gray;options=bold>%s</><fg=white>%s</><fg=blue;options=bold>:</> <fg=white>%s</>',
                        $lineNumberPre,
                        $lineNumberPost,
                        $contentLine,
                    );
                }

                $this->symfonyStyle->writeln($result);
            }

            ++$lineCnt;
        }
    }
}
