<?php

declare(strict_types=1);

namespace PHPLint\Process;

enum StatusEnum: string
{
    case ERROR = 'error';
    case NOTICE = 'notice';
    case OK = 'ok';
    case RUNNING = 'running';
    case WARNING = 'warning';
}
