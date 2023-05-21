<?php

declare(strict_types=1);

use PHPLint\Config\LintConfig;

return static function (LintConfig $lintConfig): LintConfig
{
    echo 'phplint config'.PHP_EOL;
    // $lintConfig->setPhpCgiExecutable('test');

    return $lintConfig;
};