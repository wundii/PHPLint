<?php

declare(strict_types=1);

namespace PHPLint\Console;

use PHPLint\Bootstrap\BootstrapConfig;
use PHPLint\Console\Commands\LintCommand;
use PHPLint\Console\Commands\LintInitCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

final class LintApplication extends BaseApplication
{
    /**
     * @var string
     */
    public const NAME = 'PHPLint';

    /**
     * @var string
     */
    public const VERSION = '0.3.1';

    public function __construct(
        LintCommand $lintCommand,
        LintInitCommand $lintInitCommand,
    ) {
        parent::__construct(self::NAME, self::VERSION);

        $this->add($lintCommand);
        $this->add($lintInitCommand);
        $this->setDefaultCommand('lint');
        $this->setDefinition($this->getInputDefinition());
    }

    public static function runExceptionally(Throwable $throwable, ?OutputInterface $output = null): int
    {
        $argv = $_SERVER['argv'] ?? [];
        $argvInput = new ArgvInput($argv);

        if (! $output instanceof OutputInterface) {
            $output = new ConsoleOutput();
        }

        $symfonyStyle = new SymfonyStyle($argvInput, $output);

        $symfonyStyle->writeln('> ' . implode(' ', $argv));
        $symfonyStyle->writeln('<fg=blue;options=bold>PHP</><fg=yellow;options=bold>Lint</> ' . self::VERSION);
        $symfonyStyle->newLine();

        $symfonyStyle->error($throwable->getMessage());
        $symfonyStyle->writeln($throwable->getTraceAsString());

        return Command::FAILURE;
    }

    private function getInputDefinition(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            ...OptionEnum::getInputDefinition($this->getDefaultConfigPath()),
        ]);
    }

    private function getDefaultConfigPath(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . BootstrapConfig::DEFAULT_CONFIG_FILE;
    }
}
