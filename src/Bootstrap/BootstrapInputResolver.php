<?php

declare(strict_types=1);

namespace PHPLint\Bootstrap;

use PHPLint\Console\OptionEnum;
use Symfony\Component\Console\Input\InputInterface;

final class BootstrapInputResolver
{
    public function __construct(
        private readonly InputInterface $argvInput
    ) {
    }

    public function getOptionValue(OptionEnum $optionEnum): ?string
    {
        $optionNames = [
            $optionEnum->getName(),
            $optionEnum->getShortcut(),
        ];

        foreach ($optionNames as $optionName) {
            if ($this->argvInput->hasParameterOption($optionName, true)) {
                $parameterOption = $this->argvInput->getParameterOption($optionName, null, true);

                if (! is_string($parameterOption)) {
                    continue;
                }

                return $parameterOption;
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function getOptionArray(OptionEnum $optionEnum): array
    {
        $argv = $_SERVER['argv'] ?? [];
        $array = [];

        foreach ($argv as $value) {
            if (! is_string($value)) {
                continue;
            }

            $needle = $optionEnum->getName() . '=';
            if (! str_starts_with($value, $needle)) {
                continue;
            }

            $array[] = substr($value, strlen($needle));
        }

        return $array;
    }

    public function hasOption(OptionEnum $optionEnum): bool
    {
        $optionNames = [
            $optionEnum->getName(),
            $optionEnum->getShortcut(),
        ];

        foreach ($optionNames as $optionName) {
            if ($this->argvInput->hasParameterOption($optionName, true)) {
                return true;
            }
        }

        return false;
    }
}
