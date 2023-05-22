<?php

declare(strict_types=1);

use PHPLint\Console\OptionEnum;
use PHPUnit\Framework\TestCase;

class OptionEnumTest extends TestCase
{
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

        foreach (OptionEnum::values() as $option) {
            $name = $option->getName();
            $this->assertFalse(in_array($name, $optionNames), "Duplicate option name: $name");
            $optionNames[] = $name;
        }

        $this->assertCount(count(OptionEnum::values()), $optionNames, 'Missing option names');
    }

    public function testAllShortcutsAreUnique()
    {
        $shortcuts = [];

        foreach (OptionEnum::values() as $option) {
            $shortcut = $option->getShortcut();
            $this->assertFalse(in_array($shortcut, $shortcuts), "Duplicate shortcut: $shortcut");
            $shortcuts[] = $shortcut;
        }

        $this->assertCount(count(OptionEnum::values()), $shortcuts, 'Missing shortcuts');
    }
}