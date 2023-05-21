<?php

declare(strict_types=1);
namespace PHPLint\Bootstrap;

use PHPLint\Config\LintConfig;

final class BootstrapConfigRequirer
{
    public function __construct(
        private readonly BootstrapConfig $bootstrapConfig
    ) {}

    public function getLintConfig(): LintConfig
    {
        $phpLintConfig = new LintConfig();

        $fn = require_once $this->bootstrapConfig->getBootstrapConfigFile();

        return $fn($phpLintConfig);
    }
}