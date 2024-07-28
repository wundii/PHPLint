<?php

declare(strict_types=1);

namespace Main\Process;

use PHPUnit\Framework\TestCase;
use ReflectionEnum;
use Wundii\PHPLint\Process\StatusEnum;

class StatusEnumTest extends TestCase
{
    public static function enumValues(): array
    {
        $reflectionEnum = new ReflectionEnum(StatusEnum::class);

        return array_map(static fn ($enum) => $enum->getValue(), $reflectionEnum->getCases());
    }

    public function testAllStatusNamesAreUnique()
    {
        $statusNames = [];

        foreach (self::enumValues() as $status) {
            $name = $status->value;
            $this->assertFalse(in_array($name, $statusNames, true), "Duplicate status name: {$name}");
            $statusNames[] = $name;
        }

        $this->assertCount(count(self::enumValues()), $statusNames, 'Missing status names');
    }
}
