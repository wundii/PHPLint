<?php

declare(strict_types=1);

namespace Wundii\PHPLint\Console\Commands;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wundii\PHPLint\Bootstrap\BootstrapConfigInitializer;
use Wundii\PHPLint\Bootstrap\BootstrapConfigResolver;
use Wundii\PHPLint\Bootstrap\BootstrapInputResolver;
use Wundii\PHPLint\Config\LintConfig;
use Wundii\PHPLint\Config\OptionEnum as ConfigOptionEnum;
use Wundii\PHPLint\Console\LintApplication;
use Wundii\PHPLint\Console\OptionEnum;
use Wundii\PHPLint\Console\Output\LintSymfonyStyle;
use Wundii\PHPLint\Finder\LintFinder;
use Wundii\PHPLint\Lint\Lint;

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
        $output->startApplication(LintApplication::vendorVersion());

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
