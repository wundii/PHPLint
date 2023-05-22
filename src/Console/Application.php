<?php

declare(strict_types=1);
namespace PHPLint\Console;

use Exception;
use PHPLint\Config\LintConfig;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

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
    public function initRun(): BaseApplication
    {
        if(!function_exists('proc_open')) {
            throw new Exception('proc_open() is disabled.');
        }

        $application = new BaseApplication(self::NAME, self::VERSION);
        $application->add(new LintCommand($this->lintConfig));
        $application->setDefaultCommand('phplint', true);
        $application->setDefinition($this->getInputDefinition());

        return $application;
    }

    /**
     * @throws Exception
     */
    public function run(): int
    {
        $baseApplication = $this->initRun();

        return $baseApplication->run();
    }

    public function runExceptionally(Exception $exception, ?OutputInterface $output = null): int
    {
        $argv = $_SERVER['argv'] ?? [];

        if(!$output instanceof OutputInterface) {
            $output = new ConsoleOutput();
        }

        $output->writeln('> ' . implode('', $argv));
        $output->writeln('<fg=blue;options=bold>PHP</><fg=yellow;options=bold>Lint</> ' . self::VERSION);
        $output->writeln('');
        $output->writeln('<error>' . $exception->getMessage() . '</error>');
        $output->writeln('');

        return Command::FAILURE;
    }

    private function getInputDefinition(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption(OptionEnum::CONFIG->getName(), OptionEnum::CONFIG->getShortcut(), InputOption::VALUE_REQUIRED, 'Path to config file', $this->getDefaultConfigPath()),
            new InputOption(OptionEnum::VERSION->getName(), OptionEnum::VERSION->getShortcut(), InputOption::VALUE_NONE, 'Display this application version'),
            new InputOption(OptionEnum::VERBOSE->getName(), OptionEnum::VERBOSE->getShortcut(), InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
            new InputOption(OptionEnum::ANSI->getName(), OptionEnum::ANSI->getShortcut(), InputOption::VALUE_NEGATABLE, 'Force (or disable --no-ansi) ANSI output', null),
            new InputOption(OptionEnum::HELP->getName(), OptionEnum::HELP->getShortcut(), InputOption::VALUE_NONE, 'Display help for the given command.'),
        ]);
    }

    private function getDefaultConfigPath(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . 'phplint.php';
    }
}