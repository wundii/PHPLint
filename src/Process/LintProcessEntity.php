<?php

declare(strict_types=1);

namespace PHPLint\Process;

use Exception;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

final class LintProcessEntity
{
    public function __construct(
        private readonly Process $process,
        private readonly SplFileInfo $splFileInfo,
    ) {
    }

    /**
     * @throws Exception
     */
    public function getProcessOutput(): LintProcessResult
    {
        if ($this->process->isRunning()) {
            throw new Exception('Process is still running');
        }

        $output = trim($this->process->getOutput());
        return new LintProcessResult(
            StatusEnum::OK,
            $this->splFileInfo->getRealPath(),
            $output,
            12
        );
    }

    public function isRunning(): bool
    {
        return $this->process->isRunning();
    }
}
