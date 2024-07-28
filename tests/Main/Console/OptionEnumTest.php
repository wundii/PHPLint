<?php

declare(strict_types=1);

namespace Wundii\PHPLint\Tests\Main\Console;

use Wundii\PHPLint\Bootstrap\BootstrapInputResolver;
use Wundii\PHPLint\Config\LintConfig;
use Wundii\PHPLint\Console\OptionEnum;
use PHPUnit\Framework\TestCase;
use ReflectionEnum;
use Symfony\Component\Console\Input\ArgvInput;

class OptionEnumTest extends TestCase
{
    public static function enumValues(): array
    {
        $reflectionEnum = new ReflectionEnum(OptionEnum::class);

        return array_map(static fn ($enum) => $enum->getValue(), $reflectionEnum->getCases());
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

    public function testCreateLintConfigFromInputDefault()
    {
        unset($_SERVER['argv']);
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput()
        );

        $expected = new LintConfig();
        $lintConfig = OptionEnum::createLintConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $lintConfig);
    }

    public function testCreateLintConfigFromInputAsyncProcess()
    {
        unset($_SERVER['argv']);
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/phplint', OptionEnum::ASYNC_PROCESS->getName(), '5'])
        );

        $expected = new LintConfig();
        $expected->asyncProcess(5);
        $lintConfig = OptionEnum::createLintConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $lintConfig);
    }

    public function testCreateLintConfigFromInputMemoryLimit()
    {
        unset($_SERVER['argv']);
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/phplint', OptionEnum::MEMORY_LIMIT->getName(), '1337K'])
        );

        $expected = new LintConfig();
        $expected->memoryLimit('1337K');
        $lintConfig = OptionEnum::createLintConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $lintConfig);
    }

    public function testCreateLintConfigFromInputPhpExtension()
    {
        unset($_SERVER['argv']);
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/phplint', OptionEnum::PHP_EXTENSION->getName(), 'php3'])
        );

        $expected = new LintConfig();
        $expected->phpExtension('php3');
        $lintConfig = OptionEnum::createLintConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $lintConfig);
    }

    public function testCreateLintConfigFromInputNoExitCode()
    {
        unset($_SERVER['argv']);
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/phplint', OptionEnum::NO_EXIT_CODE->getName()])
        );

        $expected = new LintConfig();
        $expected->disableExitCode();
        $lintConfig = OptionEnum::createLintConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $lintConfig);
    }

    public function testCreateLintConfigFromInputNoProgressBar()
    {
        unset($_SERVER['argv']);
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/phplint', OptionEnum::NO_PROGRESS_BAR->getName()])
        );

        $expected = new LintConfig();
        $expected->disableProcessBar();
        $lintConfig = OptionEnum::createLintConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $lintConfig);
    }

    public function testCreateLintConfigFromInputPaths()
    {
        $paths = [
            '--paths=src/',
            '--paths=vendor/',
        ];
        $_SERVER['argv'] = $paths;
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/phplint', ...$paths])
        );

        $expected = new LintConfig();
        $expected->paths([
            'src/',
            'vendor/',
        ]);
        $lintConfig = OptionEnum::createLintConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $lintConfig);

        unset($_SERVER['argv']);
    }

    public function testCreateLintConfigFromInputSkip()
    {
        $skip = [
            '--skip=test/',
            '--skip=var/',
        ];
        $_SERVER['argv'] = $skip;
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/phplint', ...$skip])
        );

        $expected = new LintConfig();
        $expected->skip([
            'test/',
            'var/',
        ]);
        $lintConfig = OptionEnum::createLintConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $lintConfig);

        unset($_SERVER['argv']);
    }

    public function testCreateLintConfigFromInputMixed()
    {
        $bootstrapInputResolver = new BootstrapInputResolver(
            new ArgvInput(['bin/phplint',
                OptionEnum::ASYNC_PROCESS->getName(), '8',
                OptionEnum::MEMORY_LIMIT->getName(), '128M',
                OptionEnum::PHP_EXTENSION->getName(), 'php4',
                OptionEnum::NO_PROGRESS_BAR->getName(),
            ])
        );

        $expected = new LintConfig();
        $expected->asyncProcess(8);
        $expected->disableProcessBar();
        $expected->memoryLimit('128M');
        $expected->phpExtension('php4');
        $lintConfig = OptionEnum::createLintConfigFromInput($bootstrapInputResolver);

        $this->assertEquals($expected, $lintConfig);
    }
}
