<?php

declare(strict_types=1);

namespace PHPLint\Process;

use PHPLint\Config\LintConfig;
use PHPLint\Config\OptionEnum;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

final class LintProcessTask
{
    /**
     * @var string
     */
    public const REGEX_ERROR = '/^(PHP\s+)?(Parse|Fatal) error:\s*?(?<error>.*?)(?: in .+? line (?<line>\d+))?$/';

    /**
     * @var string
     */
    public const REGEX_WARNING = '/^(PHP\s+)?(Warning|Deprecated|Notice):\s*?(?<error>.+?)(?: in .+? line (?<line>\d+))?$/';

    public function __construct(
        private readonly LintConfig $lintConfig,
        private readonly Process $process,
        private readonly SplFileInfo $splFileInfo,
    ) {
    }

    public function getProcessResult(): LintProcessResult
    {
        $fileRealPath = $this->splFileInfo->getRealPath();

        if ($this->isRunning()) {
            return new LintProcessResult(StatusEnum::RUNNING, $fileRealPath, 'Process is still running');
        }

        $output = trim($this->process->getOutput());
        $outputExplode = explode("\n", $output);
        $result = array_shift($outputExplode);

        $matchedError = ! str_contains($result, 'No syntax errors detected');
        $matchedWarning = preg_match('#(Warning:|Deprecated:)#', $result);
        $matchedInfo = str_contains($result, 'Notice:');
        $isConsoleNotice = $this->lintConfig->getBoolean(OptionEnum::CONSOLE_NOTICE);
        $isConsoleWarning = $this->lintConfig->getBoolean(OptionEnum::CONSOLE_WARNING);

        if ($matchedError && ! $matchedWarning && ! $matchedInfo) {
            return $this->createLintProcessResult(StatusEnum::ERROR, $fileRealPath, self::REGEX_ERROR, $result);
        }

        if ($isConsoleWarning && $matchedWarning) {
            return $this->createLintProcessResult(StatusEnum::WARNING, $fileRealPath, self::REGEX_WARNING, $result);
        }

        if ($isConsoleNotice && $matchedInfo) {
            return $this->createLintProcessResult(StatusEnum::NOTICE, $fileRealPath, self::REGEX_WARNING, $result);
        }

        return new LintProcessResult(StatusEnum::OK, $fileRealPath);
    }

    public function isRunning(): bool
    {
        return $this->process->isRunning();
    }

    private function createLintProcessResult(StatusEnum $statusEnum, string $filename, string $pattern, string $result): LintProcessResult
    {
        $message = '';
        $line = null;

        $matched = preg_match($pattern, $result, $match);
        if ($matched !== false && $matched > 0) {
            $message = trim($match['error']);
            $line = (int) $match['line'];
        }

        return new LintProcessResult($statusEnum, $filename, $message, $line);
    }
}
