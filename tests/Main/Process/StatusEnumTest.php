<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Process;

use PHPLint\Process\StatusEnum;
use PHPUnit\Framework\TestCase;
use ReflectionEnum;

class StatusEnumTest extends TestCase
{
    public static function enumValues(): array
    {
        $values = [];

        $reflectionEnum = new ReflectionEnum(StatusEnum::class);
        foreach ($reflectionEnum->getCases() as $case) {
            $values[] = $case->getValue();
        }

        return $values;
    }

    public function testAllStatusNamesAreUnique()
    {
        $statusNames = [];

        foreach (self::enumValues() as $status) {
            $name = $status->value;
            $this->assertFalse(in_array($name, $statusNames, true), "Duplicate option name: {$name}");
            $statusNames[] = $name;
        }

        $this->assertCount(count(self::enumValues()), $statusNames, 'Missing option names');
    }
}
