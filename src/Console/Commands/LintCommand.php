<?php

declare(strict_types=1);

namespace PHPLint\Console\Commands;

use Exception;
use PHPLint\Bootstrap\BootstrapConfigInitializer;
use PHPLint\Bootstrap\BootstrapConfigResolver;
use PHPLint\Config\LintConfig;
use PHPLint\Console\LintApplication;
use PHPLint\Console\Output\LintConsoleOutput;
use PHPLint\Finder\LintFinder;
use PHPLint\Lint\Lint;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LintCommand extends Command
{
    public function __construct(
        private readonly BootstrapConfigInitializer $bootstrapConfigInitializer,
        private readonly BootstrapConfigResolver $bootstrapConfigResolver,
        private readonly LintConsoleOutput $lintConsoleOutput,
        private readonly LintConfig $lintConfig,
        private readonly LintFinder $lintFinder,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('lint');
        $this->setDescription('Start to lint your PHP files');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! $this->bootstrapConfigResolver->isConfigFileExists($input)) {
            $this->bootstrapConfigInitializer->createConfig((string) getcwd());
            return self::SUCCESS;
        }

        $startExecuteTime = microtime(true);

        $this->lintConsoleOutput->startApplication(LintApplication::VERSION);

        $lintFinder = $this->lintFinder->getFilesFromLintConfig($this->lintConfig);

        $lint = new Lint($this->lintConsoleOutput, $this->lintConfig, $lintFinder);
        $lint->run();

        $usageExecuteTime = Helper::formatTime(microtime(true) - $startExecuteTime);

        $exitCode = (int) $this->lintConsoleOutput->finishApplication($usageExecuteTime);

        if ($this->lintConfig->isIgnoreExitCode()) {
            return self::SUCCESS;
        }

        return $exitCode;
    }
}
