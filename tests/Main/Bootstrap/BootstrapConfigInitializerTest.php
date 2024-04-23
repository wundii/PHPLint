<?php

declare(strict_types=1);

namespace PHPLint\Tests\Main\Bootstrap;

use PHPLint\Bootstrap\BootstrapConfigInitializer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class BootstrapConfigInitializerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateConfigShowsWarningIfConfigFileExists()
    {
        // Arrange
        $filesystem = $this->createMock(Filesystem::class);
        $symfonyStyle = $this->createMock(SymfonyStyle::class);
        $initializer = new BootstrapConfigInitializer($filesystem, $symfonyStyle);
        $projectDirectory = '/path/to/project';

        // Set up expectations
        $filesystem->method('exists')->willReturn(true);
        $symfonyStyle->expects($this->once())->method('warning')->with('The "phplint.php" config already exists.');

        // Act
        $initializer->createConfig($projectDirectory);
    }

    /**
     * @throws Exception
     */
    public function testCreateConfigAsksConfirmationAndReturnsIfUserDoesNotConfirm()
    {
        // Arrange
        $filesystem = $this->createMock(Filesystem::class);
        $symfonyStyle = $this->createMock(SymfonyStyle::class);
        $initializer = new BootstrapConfigInitializer($filesystem, $symfonyStyle);
        $projectDirectory = '/path/to/project';

        // Set up expectations
        $filesystem->method('exists')->willReturn(false);
        $symfonyStyle->expects($this->once())->method('ask')->willReturn('no');

        // Act
        $initializer->createConfig($projectDirectory);
    }

    /**
     * @throws Exception
     */
    public function testCreateConfigCopiesConfigTemplateAndShowsSuccessMessage()
    {
        // Arrange
        $filesystem = $this->createMock(Filesystem::class);
        $symfonyStyle = $this->createMock(SymfonyStyle::class);
        $initializer = new BootstrapConfigInitializer($filesystem, $symfonyStyle);
        $projectDirectory = '/path/to/project';
        $configFile = $projectDirectory . DIRECTORY_SEPARATOR . 'phplint.php';

        // Set up expectations
        $filesystem->method('exists')->willReturn(false);
        $symfonyStyle->method('ask')->willReturn('yes');
        $filesystem->expects($this->once())->method('copy')->with(
            getcwd() . '/templates/phplint.php.dist',
            $configFile
        );
        $symfonyStyle->expects($this->once())->method('success')->with('The config file was generated! You can now run "bin/phplint" to lint your code.');

        // Act
        $initializer->createConfig($projectDirectory);
    }

    /**
     * @throws Exception
     */
    public function testCreateConfigShowsErrorMessageIfConfigFileCouldNotBeGenerated()
    {
        // Arrange
        $filesystem = $this->createMock(Filesystem::class);
        $symfonyStyle = $this->createMock(SymfonyStyle::class);
        $initializer = new BootstrapConfigInitializer($filesystem, $symfonyStyle);
        $projectDirectory = '/path/to/project';

        // Set up expectations
        $filesystem->method('exists')->willReturn(false);
        $symfonyStyle->method('ask')->willReturn('yes');
        $filesystem->method('copy')->willThrowException(new IOException(sprintf('Failed to copy "%s" to "%s".', 'source/file.txt', 'target/file.txt')));
        $symfonyStyle->expects($this->once())->method('error')->with('Failed to copy "source/file.txt" to "target/file.txt".');

        // Act
        $initializer->createConfig($projectDirectory);
    }
}
