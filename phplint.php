<?php

declare(strict_types=1);

use PHPLint\Config\LintConfig;

return static function (LintConfig $lintConfig): void
{
    $lintConfig->setPhpCgiExecutable('php7');
    $lintConfig->setPaths(['src']);
};