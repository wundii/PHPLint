<?php

declare(strict_types=1);

namespace PHPLint\Bootstrap;

use Exception;
use PHPLint\Console\OptionEnum;
use Symfony\Component\Console\Input\InputInterface;

final class BootstrapConfigResolver
{
    /**
     * @throws Exception
     */
    public function getBootstrapConfig(InputInterface $argvInput): BootstrapConfig
    {
        $configFile = $this->resolveFromInput($argvInput);

        return new BootstrapConfig($configFile);
    }

    public function isConfigFileExists(InputInterface $argvInput): bool
    {
        $configFile = $this->resolveFromInput($argvInput);

        return $configFile !== null;
    }

    private function resolveFromInput(InputInterface $argvInput): ?string
    {
        $configFile = $this->getOptionValue($argvInput, [OptionEnum::CONFIG->getName(), OptionEnum::CONFIG->getShortcut()]);
        if ($configFile === null) {
            $configFile = getcwd() . DIRECTORY_SEPARATOR . BootstrapConfig::DEFAULT_CONFIG_FILE;
        }

        if (! file_exists($configFile)) {
            return null;
        }

        return $configFile;
    }

    /**
     * @param array<string> $optionNames
     */
    private function getOptionValue(InputInterface $argvInput, array $optionNames): ?string
    {
        foreach ($optionNames as $optionName) {
            if ($argvInput->hasParameterOption($optionName, true)) {
                return $argvInput->getParameterOption($optionName, null, true);
            }
        }

        return null;
    }
}
