<?php

declare(strict_types=1);

namespace Wundii\PHPLint\Console;

enum OutputColorEnum: string
{
    case BLACK = 'black';
    case RED = 'red';
    case GREEN = 'green';
    case YELLOW = 'yellow';
    case BLUE = 'blue';
    case MAGENTA = 'magenta';
    case CYAN = 'cyan';
    case WHITE = 'white';
    case GRAY = 'gray';
    case BRIGHT_RED = 'bright-red';
    case BRIGHT_GREEN = 'bright-green';
    case BRIGHT_YELLOW = 'bright-yellow';
    case BRIGHT_BLUE = 'bright-blue';
    case BRIGHT_MAGENTA = 'bright-magenta';
    case BRIGHT_CYAN = 'bright-cyan';
    case BRIGHT_WHITE = 'bright-white';

    public function getBrightEnum(): self
    {
        return match ($this) {
            self::RED => self::BRIGHT_RED,
            self::GREEN => self::BRIGHT_GREEN,
            self::YELLOW => self::BRIGHT_YELLOW,
            self::BLUE => self::BRIGHT_BLUE,
            self::MAGENTA => self::BRIGHT_MAGENTA,
            self::CYAN => self::BRIGHT_CYAN,
            self::WHITE => self::BRIGHT_WHITE,
            default => $this,
        };
    }

    public function getBrightValue(): string
    {
        return $this->getBrightEnum()->value;
    }
}
