<?php

declare(strict_types=1);

namespace PHPLint\Resolver\Config;

use PHPLint\Config\LintConfigParameter;
use PHPLint\Config\OptionEnum;

final class LintSkipPathsResolver
{
    /**
     * @return array<string>
     */
    public function resolve(LintConfigParameter $lintConfigParameter): array
    {
        $paths = [];
        $skip = $lintConfigParameter->getArrayWithStrings(OptionEnum::SKIP);

        foreach ($skip as $path) {
            if (class_exists($path)) {
                continue;
            }

            if (! str_starts_with($path, (string) getcwd())) {
                if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
                    $path = substr($path, 1);
                }

                $path = getcwd() . DIRECTORY_SEPARATOR . $path;
            }

            if (! is_dir($path)) {
                continue;
            }

            $realPath = realpath($path);

            if ($realPath !== false) {
                $paths[] = $realPath;
            }
        }

        return $paths;
    }
}
