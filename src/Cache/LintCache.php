<?php

declare(strict_types=1);

namespace Wundii\PHPLint\Cache;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\TraceableAdapter;
use Symfony\Contracts\Cache\ItemInterface;

final class LintCache extends TraceableAdapter
{
    public function isMd5FileValid(string $filename): bool
    {
        try {
            $cacheItem = $this->getItem($this->convertFilename($filename));
        } catch (InvalidArgumentException) {
            return false;
        }

        if (! $cacheItem->isHit()) {
            return false;
        }

        if (! file_exists($filename)) {
            return false;
        }

        $md5File = md5_file($filename);
        if ($md5File === false) {
            return false;
        }

        return $md5File === $cacheItem->get();
    }

    public function setMd5File(string $filename): bool
    {
        try {
            $cacheItem = $this->getItem($this->convertFilename($filename));
        } catch (InvalidArgumentException) {
            return false;
        }

        if (! file_exists($filename)) {
            return false;
        }

        $md5File = md5_file($filename);
        if ($md5File === false) {
            return false;
        }

        $cacheItem->set($md5File);
        return $this->save($cacheItem);
    }

    public function convertFilename(string $filename): string
    {
        return str_replace(str_split(ItemInterface::RESERVED_CHARACTERS), '_', $filename);
    }
}
