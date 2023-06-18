<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Console;

use PHPLint\Console\OutputColorEnum;
use PHPUnit\Framework\TestCase;
use ReflectionEnum;

class OutputColorEnumTest extends TestCase
{
    public static function enumValues(): array
    {
        $values = [];

        $reflectionEnum = new ReflectionEnum(OutputColorEnum::class);
        foreach ($reflectionEnum->getCases() as $case) {
            $values[] = $case->getValue();
        }

        return $values;
    }

    public function testAllOutputColorNamesAreUnique()
    {
        $statusNames = [];

        foreach (self::enumValues() as $outputColor) {
            $name = $outputColor->value;
            $this->assertFalse(in_array($name, $statusNames, true), "Duplicate color name: {$name}");
            $statusNames[] = $name;
        }

        $this->assertCount(count(self::enumValues()), $statusNames, 'Missing color names');
    }

    public function testAllBrightEnum()
    {
        $notBrightColors = [
            OutputColorEnum::BLACK,
            OutputColorEnum::GRAY,
        ];

        foreach (self::enumValues() as $outputColor) {
            /** @var OutputColorEnum $outputColor */
            $outputColorEnum = $outputColor->getBrightEnum();
            if (in_array($outputColorEnum, $notBrightColors, true)) {
                $this->assertStringNotContainsString('bright-', $outputColorEnum->value);
            } else {
                $this->assertStringContainsString('bright-', $outputColorEnum->value);
            }
        }
    }
}
