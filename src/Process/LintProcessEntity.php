<?php

declare(strict_types=1);

namespace PHPLint\Process;

use Exception;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

final class LintProcessEntity
{
    /**
     * @var string
     */
    public const REGEX_ERROR = '/^(PHP\s+)?(Parse|Fatal) error:\s*(?:\w+ error,\s*)?(?<error>.+?)\s+in\s+.+?\s*line\s+(?<line>\d+)/';

    /**
     * @var string
     */
    public const REGEX_WARNING = '/^(PHP\s+)?(Warning|Deprecated|Notice):\s*?(?<error>.+?)\s+in\s+.+?\s*line\s+(?<line>\d+)/';

    public function __construct(
        private readonly Process $process,
        private readonly SplFileInfo $splFileInfo,
    ) {
    }

    /**
     * @throws Exception
     */
    public function getProcessResult(): LintProcessResult
    {
        if ($this->isRunning()) {
            throw new Exception('Process is still running');
        }

        $output = trim($this->process->getOutput());

        $outputExplode = explode("\n", $output);
        $result = array_shift($outputExplode);
        $fileRealPath = $this->splFileInfo->getRealPath();

        if (! str_contains($result, 'No syntax errors detected')) {
            return $this->createLintProcessResult(StatusEnum::ERROR, $fileRealPath, self::REGEX_ERROR, $result);
        }

        $matched = preg_match('#(Warning:|Deprecated:|Notice:)#', $result);
        if ($matched !== false && $matched > 0) {
            return $this->createLintProcessResult(StatusEnum::WARNING, $fileRealPath, self::REGEX_WARNING, $result);
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
            $message = $match['error'];
            $line = (int) $match['line'];
        }

        return new LintProcessResult($statusEnum, $filename, $message, $line);
    }
}
