<?php

declare(strict_types=1);

namespace PHPLint\Config;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class LintConfig
{
    private string $phpCgiExecutable = 'php';

    /**
     * @var array<string>
     */
    private array $paths = [];

    /**
     * @var array<string>
     */
    private array $skip = [];

    /**
     * @var array<string>
     */
    private array $sets = [];

    public function __construct(
        public ContainerBuilder $containerBuilder
    ) {
    }

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
        if ($this->paths === []) {
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

    /**
     * @throws Exception
     */
    public function getService(string $id): ?object
    {
        return $this->containerBuilder->get($id);
    }
}
