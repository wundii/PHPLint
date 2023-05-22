<?php

declare(strict_types=1);
namespace PHPLint\Console;

use ReflectionEnum;

enum OptionEnum: string
{
    /**
     * @var string
     */
    private const PRE_NAME = '--';

    /**
     * @var string
     */
    private const PRE_SHORTCUT = '-';

    case ANSI = 'ansi';
    case CONFIG = 'config';
    case HELP = 'help';
    case VERBOSE = 'verbose';
    case VERSION = 'version';

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

    /**
     * @return array<OptionEnum>
     */
    public static function values(): array
    {
        $values = [];

        $reflectionEnum = new ReflectionEnum(self::class);
        foreach ($reflectionEnum->getCases() as $case) {
            $values[] = $case->getValue();
        }

        return $values;
    }
}
