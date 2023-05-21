<?php

declare(strict_types=1);
namespace PHPLint\Console;

use Exception;
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

    /**
     * @throws Exception
     */
    public function run(): int
    {
        $application = new BaseApplication(self::NAME, self::VERSION);
        $application->add(new LintCommand());
        $application->setDefaultCommand('phplint', true);

        return $application->run();
    }
}