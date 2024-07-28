<?php

declare(strict_types=1);

namespace Wundii\PHPLint\Config;

use Webmozart\Assert\Assert;

class LintConfigParameter
{
    /**
     * @var array <string, mixed>
     */
    private array $parameters = [];

    public function setParameter(OptionEnum $optionEnum, mixed $value): void
    {
        $this->parameters[$optionEnum->value] = $value;
    }

    public function has(OptionEnum $optionEnum): bool
    {
        return array_key_exists($optionEnum->value, $this->parameters);
    }

    public function getBoolean(OptionEnum $optionEnum): bool
    {
        $parameter = $this->getParameter($optionEnum);

        Assert::boolean($parameter);

        return $parameter;
    }

    public function getInteger(OptionEnum $optionEnum): int
    {
        $parameter = $this->getParameter($optionEnum);

        Assert::integer($parameter);

        return $parameter;
    }

    public function getString(OptionEnum $optionEnum): string
    {
        $parameter = $this->getParameter($optionEnum);

        Assert::string($parameter);

        return $parameter;
    }

    /**
     * @return string[]
     */
    public function getArrayWithStrings(OptionEnum $optionEnum): array
    {
        $parameter = $this->getParameter($optionEnum, []);

        Assert::isArray($parameter);
        Assert::allString($parameter);

        return $parameter;
    }

    public function getParameter(OptionEnum $optionEnum, mixed $default = null): mixed
    {
        return $this->parameters[$optionEnum->value] ?? $default;
    }
}
