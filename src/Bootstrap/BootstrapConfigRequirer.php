<?php

declare(strict_types=1);

namespace PHPLint\Bootstrap;

use Exception;
use PHPLint\Config\LintConfig;
use ReflectionFunction;
use ReflectionNamedType;

final class BootstrapConfigRequirer
{
    public function __construct(
        private readonly BootstrapConfig $bootstrapConfig
    ) {
    }

    /**
     * @throws Exception
     */
    public function loadConfigFile(LintConfig $lintConfig): LintConfig
    {
        $bootstrapConfigFile = $this->bootstrapConfig->getBootstrapConfigFile();
        if ($bootstrapConfigFile === null) {
            return $lintConfig;
        }

        $fn = require_once $this->bootstrapConfig->getBootstrapConfigFile();

        if (! is_callable($fn)) {
            throw new Exception('BootstrapConfig ' . $this->bootstrapConfig->getBootstrapConfigFile() . ' file is not callable.');
        }

        /* phpstan bug: Parameter #1 $function of class ReflectionFunction constructor expects Closure|string, callable(): mixed given. */
        /* @phpstan-ignore-next-line */
        $reflectionFunction = new ReflectionFunction($fn);

        if ($reflectionFunction->getNumberOfParameters() === 0) {
            throw new Exception('BootstrapConfig ' . $this->bootstrapConfig->getBootstrapConfigFile() . ' file has no parameters.');
        }

        foreach ($reflectionFunction->getParameters() as $reflectionParameter) {
            if (
                $reflectionParameter->hasType()
                && $reflectionParameter->getType() instanceof ReflectionNamedType
                && $reflectionParameter->getType()->getName() === LintConfig::class
            ) {
                break;
            }

            // lintconfig parameter must be on the first position
            throw new Exception('BootstrapConfig ' . $this->bootstrapConfig->getBootstrapConfigFile() . ' file has no lintconfig parameter.');
        }

        $fn($lintConfig);

        return $lintConfig;
    }
}
