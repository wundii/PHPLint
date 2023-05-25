<?php

declare(strict_types=1);

use PHPLint\Config\LintConfig;

return static function (LintConfig $lintConfig): void
{
    dump('testausgabe');

    $lintConfig->setPhpCgiExecutable('php7');
    $lintConfig->setPaths(['src']);
};