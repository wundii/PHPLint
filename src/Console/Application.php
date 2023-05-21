<?php

declare(strict_types=1);
namespace PHPLint\Console;

use Exception;
use PHPLint\Config\LintConfig;
use Symfony\Component\Console\Application as BaseApplication;
final class Application
{
    /**
     * @var string
     */
    public const NAME = 'PHPLint';

    /**
     * @var string
     */
    public const VERSION = '0.0.1';

    public function __construct(
        private readonly LintConfig $lintConfig
    ) {}

    /**
     * @throws Exception
     */
    public function run(): int
    {
        if(!function_exists('proc_open')) {
            throw new Exception('proc_open() is disabled.');
        }

        $application = new BaseApplication(self::NAME, self::VERSION);
        $application->add(new LintCommand($this->lintConfig));
        $application->setDefaultCommand('phplint', true);

        return $application->run();
    }
}