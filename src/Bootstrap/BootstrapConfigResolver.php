<?php

declare(strict_types=1);

namespace PHPLint\Bootstrap;

use Exception;
use PHPLint\Console\OptionEnum;
use Symfony\Component\Console\Input\ArgvInput;

final class BootstrapConfigResolver
{
    /**
     * @throws Exception
     */
    public function getBootstrapConfig(ArgvInput $argvInput): BootstrapConfig
    {
        $configFile = $this->resolveFromInput($argvInput);

        return new BootstrapConfig($configFile);
    }

    /**
     * @throws Exception
     */
    private function resolveFromInput(ArgvInput $argvInput): string
    {
        $configFile = $this->getOptionValue($argvInput, [OptionEnum::CONFIG->getName(), OptionEnum::CONFIG->getShortcut()]);
        if ($configFile === null) {
            $configFile = getcwd() . DIRECTORY_SEPARATOR . 'phplint.php';
        }

        if (! file_exists($configFile)) {
            throw new Exception('BootstrapConfig ' . $configFile . ' file does not exist.');
        }

        return $configFile;
    }

    /**
     * @param array<string> $optionNames
     */
    private function getOptionValue(ArgvInput $argvInput, array $optionNames): ?string
    {
        foreach ($optionNames as $optionName) {
            if ($argvInput->hasParameterOption($optionName, true)) {
                return $argvInput->getParameterOption($optionName, null, true);
            }
        }

        return null;
    }
}
