<?php

declare(strict_types=1);

namespace PHPLint\Config;

enum OptionEnum: string
{
    /**
     * @internal
     */
    case ASYNC_PROCESS = 'async-process';

    /**
     * @internal
     */
    case CACHE = 'cache';

    /**
     * @internal
     */
    case CACHE_DIRECTORY = 'cache-directory';

    /**
     * @internal
     */
    case CONSOLE_NOTICE = 'console-notice';

    /**
     * @internal
     */
    case CONSOLE_WARNING = 'console-warning';

    /**
     * @internal
     */
    case MEMORY_LIMIT = 'memory-limit';

    /**
     * @internal
     */
    case NO_EXIT_CODE = 'no-exit-code';

    /**
     * @internal
     */
    case NO_PROGRESS_BAR = 'no-progress-bar';

    /**
     * @internal
     */
    case PATHS = 'paths';

    /**
     * @internal
     */
    case PHP_CGI_EXECUTABLE = 'php-cgi-executable';

    /**
     * @internal
     */
    case SKIP = 'skip';

    /**
     * @internal
     */
    case TEST = 'test';
}
