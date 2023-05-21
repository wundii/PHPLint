<?php

declare(strict_types=1);
namespace PHPLint\Bootstrap;

use Exception;
use Symfony\Component\Console\Input\ArgvInput;

final class BootstrapConfigResolver
{
    /**
     * @throws Exception
     */
    public function getBootstrapConfig(): BootstrapConfig
    {
        $configFile = $this->resolveFromInput(new ArgvInput());

        return new BootstrapConfig($configFile);
    }

    /**
     * @throws Exception
     */
    private function resolveFromInput(ArgvInput $argvInput): string
    {
        $configFile = $this->getOptionValue($argvInput, ['--config', '-c']);
        if ($configFile === null) {

            $configFile = getcwd() . DIRECTORY_SEPARATOR . 'phplint.php';
        }

        if(!file_exists($configFile)) {
            throw new Exception('Config ' . $configFile . ' file does not exist.');
        }

        $realpath = realpath($configFile);
        if($realpath === false) {
            throw new Exception('Config ' . $configFile . ' file is not readable.');
        }

        return $realpath;
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