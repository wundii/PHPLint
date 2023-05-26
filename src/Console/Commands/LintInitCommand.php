<?php

declare(strict_types=1);

namespace PHPLint\Console\Commands;

use PHPLint\Bootstrap\BootstrapConfigInitializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LintInitCommand extends Command
{
    public function __construct(
        private readonly BootstrapConfigInitializer $bootstrapConfigInitializer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('init');
        $this->setDescription('Create a new PHPLint configuration file if it does not exist');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bootstrapConfigInitializer->createConfig((string) getcwd());
        return self::SUCCESS;
    }
}
