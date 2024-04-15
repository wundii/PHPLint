<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Config;

use PHPLint\Config\LintConfigParameter;
use PHPLint\Config\OptionEnum;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

class LintConfigParameterTest extends TestCase
{
    public function testHasSetAndGetAParameter()
    {
        $lintConfigParameter = new LintConfigParameter();

        $this->assertFalse($lintConfigParameter->has(OptionEnum::TEST));
        $this->assertNull($lintConfigParameter->getParameter(OptionEnum::TEST));
        $this->assertSame('', $lintConfigParameter->getParameter(OptionEnum::TEST, ''));
        $this->assertSame([], $lintConfigParameter->getParameter(OptionEnum::TEST, []));

        $lintConfigParameter->setParameter(OptionEnum::TEST, 'php');
        $this->assertTrue($lintConfigParameter->has(OptionEnum::TEST));
        $this->assertEquals('php', $lintConfigParameter->getParameter(OptionEnum::TEST));
    }

    public function testGetBoolean()
    {
        $lintConfigParameter = new LintConfigParameter();
        $lintConfigParameter->setParameter(OptionEnum::TEST, true);
        $this->assertTrue($lintConfigParameter->getBoolean(OptionEnum::TEST));

        $lintConfigParameter->setParameter(OptionEnum::TEST, false);
        $this->assertFalse($lintConfigParameter->getBoolean(OptionEnum::TEST));

        $this->expectException(InvalidArgumentException::class);
        $lintConfigParameter->setParameter(OptionEnum::TEST, 'fail');
        $lintConfigParameter->getBoolean(OptionEnum::TEST);
    }

    public function testGetInteger()
    {
        $lintConfigParameter = new LintConfigParameter();
        $lintConfigParameter->setParameter(OptionEnum::TEST, 1234);
        $this->assertEquals(1234, $lintConfigParameter->getInteger(OptionEnum::TEST));

        $this->expectException(InvalidArgumentException::class);
        $lintConfigParameter->setParameter(OptionEnum::TEST, 'fail');
        $lintConfigParameter->getInteger(OptionEnum::TEST);
    }

    public function testGetString()
    {
        $lintConfigParameter = new LintConfigParameter();
        $lintConfigParameter->setParameter(OptionEnum::TEST, 'abcd');

        $this->assertEquals('abcd', $lintConfigParameter->getString(OptionEnum::TEST));

        $this->expectException(InvalidArgumentException::class);
        $lintConfigParameter->setParameter(OptionEnum::TEST, 1234);
        $lintConfigParameter->getString(OptionEnum::TEST);
    }

    public function testGetArrayWithStrings()
    {
        $lintConfigParameter = new LintConfigParameter();
        $lintConfigParameter->setParameter(OptionEnum::TEST, ['abcd', 'efgh']);
        $this->assertEquals(['abcd', 'efgh'], $lintConfigParameter->getArrayWithStrings(OptionEnum::TEST));

        $this->expectException(InvalidArgumentException::class);
        $lintConfigParameter->setParameter(OptionEnum::TEST, 'fail');
        $lintConfigParameter->getArrayWithStrings(OptionEnum::TEST);
    }

    public function testGetArrayWithStringsWithWrongValue()
    {
        $lintConfigParameter = new LintConfigParameter();
        $lintConfigParameter->setParameter(OptionEnum::TEST, ['abcd', 1234]);

        $this->expectException(InvalidArgumentException::class);
        $lintConfigParameter->getArrayWithStrings(OptionEnum::TEST);
    }
}
