<?php

declare(strict_types=1);

namespace PHPLint\Process;

final class LintProcessResult
{
    public function __construct(
        private readonly StatusEnum $statusEnum,
        private readonly string $filename,
        private readonly string $result = '',
        private readonly ?int $line = null,
    ) {
    }

    public function getStatus(): StatusEnum
    {
        return $this->statusEnum;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getLine(): ?int
    {
        return $this->line;
    }
}
