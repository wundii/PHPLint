<?php

declare(strict_types=1);

namespace PHPLint\Console\Commands;

use PHPLint\Bootstrap\BootstrapConfigInitializer;
use PHPLint\Bootstrap\BootstrapConfigResolver;
use PHPLint\Console\LintApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class LintCheckCommand extends Command
{
    public function __construct(
        private readonly BootstrapConfigInitializer $bootstrapConfigInitializer,
        private readonly BootstrapConfigResolver $bootstrapConfigResolver,
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('lint');
        $this->setDescription('Start to lint your PHP files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! $this->bootstrapConfigResolver->isConfigFileExists($input)) {
            $this->bootstrapConfigInitializer->createConfig((string) getcwd());
            return self::SUCCESS;
        }

        $argv = $_SERVER['argv'] ?? [];

        $output->writeln('> ' . implode('', $argv));
        $output->writeln('<fg=blue;options=bold>PHP</><fg=yellow;options=bold>Lint</> ' . LintApplication::VERSION);
        $output->writeln('');

        $this->symfonyStyle->success('Success');

        return self::SUCCESS;
    }
}
