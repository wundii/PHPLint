<?php

declare(strict_types=1);

namespace PHPLint\Bootstrap;

use Exception;

final class BootstrapConfig
{
    /**
     * @throws Exception
     */
    public function __construct(
        private readonly string $bootstrapConfigFile
    ) {
        if (! file_exists($bootstrapConfigFile)) {
            throw new Exception('BootstrapConfig ' . $bootstrapConfigFile . ' file does not exist.');
        }

        if (! is_readable($bootstrapConfigFile)) {
            throw new Exception('BootstrapConfig ' . $bootstrapConfigFile . ' file is not readable.');
        }

        if (! is_file($bootstrapConfigFile)) {
            throw new Exception('BootstrapConfig ' . $bootstrapConfigFile . ' file is not a file.');
        }
    }

    public function getBootstrapConfigFile(): string
    {
        return $this->bootstrapConfigFile;
    }
}
