<?php

declare(strict_types=1);
namespace PHPLint\Console;

use Exception;
use PHPLint\Config\LintConfig;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class Application
{
    /**
     * @var string
     */
    public const NAME = 'PHPLint';

    /**
     * @var string
     */
    public const VERSION = '0.0.1';

    public function __construct(
        private readonly LintConfig $lintConfig
    ) {}

    /**
     * @throws Exception
     */
    public function run(): int
    {
        if(!function_exists('proc_open')) {
            throw new Exception('proc_open() is disabled.');
        }

        $application = new BaseApplication(self::NAME, self::VERSION);
        $application->add(new LintCommand($this->lintConfig));
        $application->setDefaultCommand('phplint', true);
        $application->setDefinition($this->getInputDefinition());

        return $application->run();
    }

    protected function getInputDefinition(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption(OptionEnum::CONFIG->value, OptionEnum::CONFIG->getShort(), InputOption::VALUE_REQUIRED, 'Path to config file', $this->getDefaultConfigPath()),
            new InputOption(OptionEnum::VERSION->value, OptionEnum::VERSION->getShort(), InputOption::VALUE_NONE, 'Display this application version'),
            new InputOption(OptionEnum::VERBOSE->value, OptionEnum::VERBOSE->getShort(), InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
            new InputOption(OptionEnum::ANSI->value, OptionEnum::ANSI->getShort(), InputOption::VALUE_NEGATABLE, 'Force (or disable --no-ansi) ANSI output', null),
            new InputOption(OptionEnum::HELP->value, OptionEnum::HELP->getShort(), InputOption::VALUE_NONE, 'Display help for the given command.'),
        ]);
    }

    private function getDefaultConfigPath(): string
    {
        return getcwd() . '/rector.php';
    }
}