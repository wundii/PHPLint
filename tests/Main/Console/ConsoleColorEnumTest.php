<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Console;

use PHPLint\Console\ConsoleColorEnum;
use PHPUnit\Framework\TestCase;
use ReflectionEnum;

class ConsoleColorEnumTest extends TestCase
{
    public static function enumValues(): array
    {
        $values = [];

        $reflectionEnum = new ReflectionEnum(ConsoleColorEnum::class);
        foreach ($reflectionEnum->getCases() as $case) {
            $values[] = $case->getValue();
        }

        return $values;
    }

    public function testAllConsoleColorNamesAreUnique()
    {
        $statusNames = [];

        foreach (self::enumValues() as $status) {
            $name = $status->value;
            $this->assertFalse(in_array($name, $statusNames, true), "Duplicate color name: {$name}");
            $statusNames[] = $name;
        }

        $this->assertCount(count(self::enumValues()), $statusNames, 'Missing color names');
    }
}
