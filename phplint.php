<?php

declare(strict_types=1);

use PHPLint\Config\LintConfig;

return static function (LintConfig $lintConfig): void {
    $lintConfig->setPaths([
        __DIR__
    ]);

    $lintConfig->setIgnoreProcessBar(true);
};