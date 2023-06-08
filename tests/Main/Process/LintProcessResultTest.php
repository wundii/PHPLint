<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Process;

use PHPLint\Process\LintProcessResult;
use PHPLint\Process\StatusEnum;
use PHPUnit\Framework\TestCase;

class LintProcessResultTest extends TestCase
{
    public function testGetters()
    {
        $status = StatusEnum::OK;
        $filename = __DIR__ . '/example.php';
        $result = 'Some lint result';
        $line = 10;

        $lintResult = new LintProcessResult($status, $filename, $result, $line);

        $this->assertSame($status, $lintResult->getStatus());
        $this->assertSame($filename, $lintResult->getFilename());
        $this->assertSame($result, $lintResult->getResult());
        $this->assertSame($line, $lintResult->getLine());
    }
}
