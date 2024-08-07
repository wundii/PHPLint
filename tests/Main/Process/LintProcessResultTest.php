<?php

declare(strict_types=1);

namespace Main\Process;

use PHPUnit\Framework\TestCase;
use Wundii\PHPLint\Process\LintProcessResult;
use Wundii\PHPLint\Process\StatusEnum;

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
