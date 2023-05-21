<?php

declare(strict_types=1);

use PHPLint\Config\LintConfig;

return static function (LintConfig $lintConfig): LintConfig
{
    dump('testausgabe');
    // $lintConfig->setPhpCgiExecutable('test');

    return $lintConfig;
};