<?php

declare(strict_types=1);

namespace Wundii\PHPLint\Bootstrap;

use Exception;

final class BootstrapConfig
{
    /**
     * @var string
     */
    public const DEFAULT_CONFIG_FILE = 'phplint.php';

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly ?string $bootstrapConfigFile
    ) {
        if ($bootstrapConfigFile !== null && ! file_exists($bootstrapConfigFile)) {
            throw new Exception('BootstrapConfig ' . $bootstrapConfigFile . ' file does not exist.');
        }

        if ($bootstrapConfigFile !== null && ! is_readable($bootstrapConfigFile)) {
            throw new Exception('BootstrapConfig ' . $bootstrapConfigFile . ' file is not readable.');
        }

        if ($bootstrapConfigFile === null) {
            return;
        }

        if (is_file($bootstrapConfigFile)) {
            return;
        }

        throw new Exception('BootstrapConfig ' . $bootstrapConfigFile . ' file is not a file.');
    }

    public function getBootstrapConfigFile(): ?string
    {
        return $this->bootstrapConfigFile;
    }
}
