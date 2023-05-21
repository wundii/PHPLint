<?php

declare(strict_types=1);
namespace PHPLint\Console;

enum OptionEnum: string
{
    case ANSI = 'ansi';
    case CONFIG = 'config';
    case HELP = 'help';
    case VERSION = 'version';
    case VERBOSE = 'verbose';

    public function getShort(): string
    {
        return match ($this) {
            self::ANSI => '',
            self::CONFIG => 'c',
            self::HELP => 'h',
            self::VERSION => 'V',
            self::VERBOSE => 'v|vv|vvv',
        };
    }
}
