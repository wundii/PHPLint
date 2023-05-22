<?php

declare(strict_types=1);

namespace PHPLint\Console;

enum OptionEnum: string
{
    case ANSI = 'ansi';
    case CONFIG = 'config';
    case HELP = 'help';
    case VERBOSE = 'verbose';
    case VERSION = 'version';

    /**
     * @var string
     */
    private const PRE_NAME = '--';

    /**
     * @var string
     */
    private const PRE_SHORTCUT = '-';

    public function getName(): string
    {
        return self::PRE_NAME . $this->value;
    }

    public function getShortcut(): string
    {
        return match ($this) {
            self::ANSI => '',
            self::CONFIG => self::PRE_SHORTCUT . 'c',
            self::HELP => self::PRE_SHORTCUT . 'h',
            self::VERBOSE => self::PRE_SHORTCUT . 'v|vv|vvv',
            self::VERSION => self::PRE_SHORTCUT . 'V',
        };
    }
}
