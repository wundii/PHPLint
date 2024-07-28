<?php

declare(strict_types=1);

namespace Main\Cache;

use Wundii\PHPLint\Cache\LintCache;
use PHPUnit\Framework\TestCase;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Filesystem\Filesystem;

class LintCacheTest extends TestCase
{
    public function testConvertFilenameReplacesReservedCharacters(): void
    {
        $adapter = new ArrayAdapter();
        $cache = new LintCache($adapter);

        $filename = '{var/test}test(file)name@at.txt';
        $expectedConvertedFilename = '_var_test_test_file_name_at.txt';

        $convertedFilename = $cache->convertFilename($filename);

        $this->assertEquals($expectedConvertedFilename, $convertedFilename);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testIsMd5FileValidReturnsTrueForValidFile(): void
    {
        $adapter = new ArrayAdapter();
        $cache = new LintCache($adapter);
        $filesystem = new Filesystem();

        $filename = $filesystem->tempnam(sys_get_temp_dir(), 'test_file_01', '.txt');
        file_put_contents($filename, 'Valid content');
        $expectedMd5 = md5_file($filename);

        $cacheItem = $cache->getItem($cache->convertFilename($filename));
        $cacheItem->set($expectedMd5);
        $cache->save($cacheItem);

        $this->assertTrue($cache->isMd5FileValid($filename));
        $this->assertEquals($expectedMd5, $cache->getItem($cache->convertFilename($filename))->get());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testIsMd5FileValidReturnsFalseForInvalidFile(): void
    {
        $adapter = new ArrayAdapter();
        $cache = new LintCache($adapter);
        $filesystem = new Filesystem();

        $filename = $filesystem->tempnam(sys_get_temp_dir(), 'test_file_02', '.txt');
        file_put_contents($filename, 'Valid content');
        $expectedMd5 = md5_file($filename);

        $cacheItem = $cache->getItem($cache->convertFilename($filename));
        $cacheItem->set($expectedMd5);
        $cache->save($cacheItem);

        file_put_contents($filename, 'Invalid content');

        $this->assertFalse($cache->isMd5FileValid($filename));
    }

    public function testIsMd5FileValidReturnsFalseForNonExistsFile(): void
    {
        $adapter = new ArrayAdapter();
        $cache = new LintCache($adapter);

        $filename = 'non_existent_file.txt';

        $this->assertFalse($cache->isMd5FileValid($filename));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testSetMd5FileReturnsTrueForValidFile(): void
    {
        $adapter = new ArrayAdapter();
        $cache = new LintCache($adapter);
        $filesystem = new Filesystem();

        $filename = $filesystem->tempnam(sys_get_temp_dir(), 'test_file_03', '.txt');
        file_put_contents($filename, 'Valid content');
        $expectedMd5 = md5_file($filename);

        $this->assertTrue($cache->setMd5File($filename));
        $this->assertEquals($expectedMd5, $cache->getItem($cache->convertFilename($filename))->get());
    }

    public function testSetMd5FileReturnsFalseForNonExistsFile(): void
    {
        $adapter = new ArrayAdapter();
        $cache = new LintCache($adapter);

        $filename = 'non_existent_file.txt';

        $this->assertFalse($cache->setMd5File($filename));
    }
}
