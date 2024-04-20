<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Console;

use PHPLint\Console\OptionEnum;
use PHPUnit\Framework\TestCase;
use ReflectionEnum;

class OptionEnumTest extends TestCase
{
    public static function enumValues(): array
    {
        $values = [];

        $reflectionEnum = new ReflectionEnum(OptionEnum::class);
        foreach ($reflectionEnum->getCases() as $case) {
            $values[] = $case->getValue();
        }

        return $values;
    }

    public function testGetName()
    {
        $this->assertEquals('--ansi', OptionEnum::ANSI->getName());
        $this->assertEquals('--config', OptionEnum::CONFIG->getName());
        $this->assertEquals('--help', OptionEnum::HELP->getName());
        $this->assertEquals('--verbose', OptionEnum::VERBOSE->getName());
        $this->assertEquals('--version', OptionEnum::VERSION->getName());
    }

    public function testGetShortcut()
    {
        $this->assertEquals('', OptionEnum::ANSI->getShortcut());
        $this->assertEquals('-c', OptionEnum::CONFIG->getShortcut());
        $this->assertEquals('-h', OptionEnum::HELP->getShortcut());
        $this->assertEquals('-v|vv|vvv', OptionEnum::VERBOSE->getShortcut());
        $this->assertEquals('-V', OptionEnum::VERSION->getShortcut());
    }

    public function testAllOptionNamesAreUnique()
    {
        $optionNames = [];

        foreach (self::enumValues() as $option) {
            $name = $option->getName();
            $this->assertFalse(in_array($name, $optionNames, true), "Duplicate option name: {$name}");
            $optionNames[] = $name;
        }

        $this->assertCount(count(self::enumValues()), $optionNames, 'Missing option names');
    }

    public function testAllShortcutsAreUnique()
    {
        $emptyShortcuts = 0;
        $shortcuts = [];

        foreach (self::enumValues() as $option) {
            $shortcut = $option->getShortcut();
            if ($shortcut === '') {
                ++$emptyShortcuts;
                continue;
            }

            $this->assertFalse(in_array($shortcut, $shortcuts, true), "Duplicate shortcut: {$shortcut}");
            $shortcuts[] = $shortcut;
        }

        $this->assertCount(count(self::enumValues()) - $emptyShortcuts, $shortcuts, 'Missing shortcuts');
    }
}
