<?php

declare(strict_types=1);
namespace PHPLint\Config;

final class LintConfig
{
    /**
     * @param array<string> $paths
     * @param array<string> $skip
     * @param array<string> $sets
     */
    public function __construct(
        private string $phpCgiExecutable = 'php',
        private array  $paths = [],
        private array  $skip = [],
        private array  $sets = [],
    ) {}

    public function getPhpCgiExecutable(): string
    {
        return $this->phpCgiExecutable;
    }

    public function setPhpCgiExecutable(string $string): void
    {
        $this->phpCgiExecutable = $string;
    }

    /**
     * @return array<string>
     */
    public function getPaths(): array
    {
        if($this->paths === []) {
            return [getcwd() . DIRECTORY_SEPARATOR];
        }

        return $this->paths;
    }

    /**
     * @param array<string> $paths
     */
    public function setPaths(array $paths): void
    {
        $this->paths = $paths;
    }

    /**
     * @return array<string>
     */
    public function getSkip(): array
    {
        return $this->skip;
    }

    /**
     * @param array<string> $skip
     */
    public function setSkip(array $skip): void
    {
        $this->skip = $skip;
    }

    /**
     * @return array<string>
     */
    public function getSets(): array
    {
        return $this->sets;
    }

    /**
     * @param array<string> $sets
     */
    public function setSets(array $sets): void
    {
        $this->sets = $sets;
    }
}