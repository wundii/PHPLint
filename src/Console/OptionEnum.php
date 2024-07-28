<?php

declare(strict_types=1);

namespace Wundii\PHPLint\Console;

use Symfony\Component\Console\Input\InputOption;
use Wundii\PHPLint\Bootstrap\BootstrapInputResolver;
use Wundii\PHPLint\Config\LintConfig;

enum OptionEnum: string
{
    case ANSI = 'ansi';
    case ASYNC_PROCESS = 'async-process';
    case CONFIG = 'config';
    case HELP = 'help';
    case INIT = 'init';
    case MEMORY_LIMIT = 'memory-limit';
    case NO_CONFIG = 'no-config';
    case NO_EXIT_CODE = 'no-exit-code';
    case NO_PROGRESS_BAR = 'no-progress-bar';
    case PATHS = 'paths';
    case PHP_EXTENSION = 'php-extension';
    case SKIP = 'skip';
    case VERBOSE = 'verbose';
    case VERSION = 'version';

    /**
     * @var string
     */
    private const PRE_NAME = '--';

    /**
     * @var string
     */
    private const PRE_SHORTCUT = '-';

    public function getName(): string
    {
        return self::PRE_NAME . $this->value;
    }

    public function getShortcut(): string
    {
        return match ($this) {
            self::CONFIG => self::PRE_SHORTCUT . 'c',
            self::HELP => self::PRE_SHORTCUT . 'h',
            self::INIT => self::PRE_SHORTCUT . 'i',
            self::VERBOSE => self::PRE_SHORTCUT . 'v|vv|vvv',
            self::VERSION => self::PRE_SHORTCUT . 'V',
            default => '',
        };
    }

    /**
     * @return InputOption[]
     */
    public static function getInputDefinition(string $defaultConfigPath): array
    {
        return [
            new InputOption(self::ASYNC_PROCESS->getName(), null, InputOption::VALUE_REQUIRED, 'Number of parallel processes'),
            new InputOption(self::ANSI->getName(), self::ANSI->getShortcut(), InputOption::VALUE_NEGATABLE, 'Force (or disable --no-ansi) ANSI output', null),
            new InputOption(self::CONFIG->getName(), self::CONFIG->getShortcut(), InputOption::VALUE_REQUIRED, 'Path to config file', $defaultConfigPath),
            new InputOption(self::HELP->getName(), self::HELP->getShortcut(), InputOption::VALUE_NONE, 'Display help for the given command.'),
            new InputOption(self::MEMORY_LIMIT->getName(), null, InputOption::VALUE_REQUIRED, 'Set memory limit for linting process'),
            new InputOption(self::NO_CONFIG->getName(), null, InputOption::VALUE_NONE, 'Start linting without a config file'),
            new InputOption(self::NO_EXIT_CODE->getName(), null, InputOption::VALUE_NONE, 'Do not exit with a non-zero code on lint errors'),
            new InputOption(self::NO_PROGRESS_BAR->getName(), null, InputOption::VALUE_NONE, 'No progress bar output'),
            new InputOption(self::PHP_EXTENSION->getName(), null, InputOption::VALUE_REQUIRED, 'Set the file name extension for searching php files'),
            new InputOption(self::PATHS->getName(), null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Paths to lint files or directories'),
            new InputOption(self::SKIP->getName(), null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Skip lint files or directories'),
            new InputOption(self::VERBOSE->getName(), self::VERBOSE->getShortcut(), InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
            new InputOption(self::VERSION->getName(), self::VERSION->getShortcut(), InputOption::VALUE_NONE, 'Display this application version'),
        ];
    }

    public static function createLintConfigFromInput(BootstrapInputResolver $bootstrapInputResolver): LintConfig
    {
        $lintConfig = new LintConfig();

        if ($bootstrapInputResolver->hasOption(self::ASYNC_PROCESS)) {
            $lintConfig->asyncProcess((int) $bootstrapInputResolver->getOptionValue(self::ASYNC_PROCESS));
        }

        if ($bootstrapInputResolver->hasOption(self::MEMORY_LIMIT)) {
            $lintConfig->memoryLimit((string) $bootstrapInputResolver->getOptionValue(self::MEMORY_LIMIT));
        }

        if ($bootstrapInputResolver->hasOption(self::NO_EXIT_CODE)) {
            $lintConfig->disableExitCode();
        }

        if ($bootstrapInputResolver->hasOption(self::NO_PROGRESS_BAR)) {
            $lintConfig->disableProcessBar();
        }

        if ($bootstrapInputResolver->hasOption(self::PATHS)) {
            $lintConfig->paths($bootstrapInputResolver->getOptionArray(self::PATHS));
        }

        if ($bootstrapInputResolver->hasOption(self::PHP_EXTENSION)) {
            $lintConfig->phpExtension((string) $bootstrapInputResolver->getOptionValue(self::PHP_EXTENSION));
        }

        if ($bootstrapInputResolver->hasOption(self::SKIP)) {
            $lintConfig->skip($bootstrapInputResolver->getOptionArray(self::SKIP));
        }

        return $lintConfig;
    }
}
