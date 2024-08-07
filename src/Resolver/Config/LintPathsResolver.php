<?php

declare(strict_types=1);

namespace Wundii\PHPLint\Resolver\Config;

use Wundii\PHPLint\Config\LintConfigParameter;
use Wundii\PHPLint\Config\OptionEnum;

final class LintPathsResolver
{
    /**
     * @var string[]
     */
    private array $paths = [];

    /**
     * @return string[]
     */
    public function resolve(LintConfigParameter $lintConfigParameter): array
    {
        $this->paths = $lintConfigParameter->getArrayWithStrings(OptionEnum::PATHS);

        if ($this->paths === []) {
            return [getcwd() . DIRECTORY_SEPARATOR];
        }

        foreach ($this->paths as $key => $path) {
            if (is_dir($path)) {
                continue;
            }

            if (is_file($path)) {
                continue;
            }

            unset($this->paths[$key]);
        }

        return array_values($this->paths);
    }
}
