<?php

declare(strict_types=1);

namespace PHPLint\Bootstrap;

use Exception;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

final class BootstrapConfigInitializer
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly SymfonyStyle $symfonyStyle
    ) {
    }

    public function createConfig(string $projectDirectory): void
    {
        $configFile = $projectDirectory . DIRECTORY_SEPARATOR . BootstrapConfig::DEFAULT_CONFIG_FILE;

        if ($this->filesystem->exists($configFile)) {
            $warningMessage = sprintf('The "%s" config already exists.', BootstrapConfig::DEFAULT_CONFIG_FILE);
            $this->symfonyStyle->warning($warningMessage);
            return;
        }

        $questionMessage = sprintf('No "%s" config found. Should we generate it for you?', BootstrapConfig::DEFAULT_CONFIG_FILE);
        $response = $this->symfonyStyle->ask($questionMessage, 'yes');
        if ($response !== 'yes') {
            return;
        }

        try {
            $this->filesystem->copy(__DIR__ . '/../../templates/phplint.php.dist', $configFile);
        } catch (Exception $exception) {
            $this->symfonyStyle->error($exception->getMessage());
            return;
        }

        $this->symfonyStyle->success('The config file was generated! You can now run "bin/phplint" to lint your code.');
    }
}
