<?php

declare(strict_types=1);

namespace PHPLint\Console\Commands;

use Exception;
use PHPLint\Bootstrap\BootstrapConfigInitializer;
use PHPLint\Bootstrap\BootstrapConfigResolver;
use PHPLint\Bootstrap\BootstrapInputResolver;
use PHPLint\Config\LintConfig;
use PHPLint\Config\OptionEnum as ConfigOptionEnum;
use PHPLint\Console\LintApplication;
use PHPLint\Console\OptionEnum;
use PHPLint\Console\Output\LintSymfonyStyle;
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
        private readonly BootstrapInputResolver $bootstrapInputResolver,
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
        $lintConfig = $this->lintConfig;
        $noConfig = $this->bootstrapInputResolver->hasOption(OptionEnum::NO_CONFIG);

        if (! $this->bootstrapConfigResolver->isConfigFileExists() || $noConfig) {
            if (! $noConfig) {
                $this->bootstrapConfigInitializer->createConfig((string) getcwd());
                return self::SUCCESS;
            }

            $lintConfig = OptionEnum::createLintConfigFromInput($this->bootstrapInputResolver);
        }

        $startExecuteTime = microtime(true);

        $output = new LintSymfonyStyle($lintConfig, $input, $output);
        $output->startApplication(LintApplication::VERSION);

        $lintFinder = $this->lintFinder->getFilesFromLintConfig($lintConfig);

        $lint = new Lint($output, $lintConfig, $lintFinder);
        $lint->run();

        $usageExecuteTime = Helper::formatTime(microtime(true) - $startExecuteTime);

        $exitCode = (int) $output->finishApplication($usageExecuteTime);

        if ($lintConfig->getBoolean(ConfigOptionEnum::NO_EXIT_CODE)) {
            return self::SUCCESS;
        }

        return $exitCode;
    }
}
