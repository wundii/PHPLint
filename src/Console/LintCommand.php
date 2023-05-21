<?php

declare(strict_types=1);
namespace PHPLint\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LintCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('phplint');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<error>foo</error>');

        return self::SUCCESS;
    }
}