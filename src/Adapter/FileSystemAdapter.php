<?php

namespace NilPortugues\Cache\Adapter;

use DateTime;
use InvalidArgumentException;
use NilPortugues\Cache\CacheAdapter;

/**
 * Class FileSystemAdapter
 * @package NilPortugues\Cache\Adapter
 */
class FileSystemAdapter extends Adapter implements CacheAdapter
{
    const CACHE_FILE_SUFFIX = '.php.cache';

    /**
     * @var CacheAdapter|null
     */
    private $nextAdapter;

    /**
     * @var InMemoryAdapter
     */
    private $inMemoryAdapter;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param string          $cacheDir
     * @param InMemoryAdapter $inMemory
     * @param CacheAdapter    $next
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($cacheDir, InMemoryAdapter $inMemory, CacheAdapter $next = null)
    {
        $this->inMemoryAdapter = $inMemory;
        $this->nextAdapter     = ($inMemory === $next) ? null : $next;

        $cacheDir = realpath($cacheDir);

        if (false === is_dir($cacheDir)) {
            throw new InvalidArgumentException(
                sprintf('The provided path %s is not a valid directory', $cacheDir)
            );
        }

        if (false === is_writable($cacheDir)) {
            throw new InvalidArgumentException(
                sprintf('The provided directory %s is not writable', $cacheDir)
            );
        }

        $this->cacheDir = $cacheDir;
    }

    /**
     * Get a value identified by $key.
     *
     * @param  string $key
     *
     * @return bool|mixed
     */
    public function get($key)
    {
        $key       = (string)$key;
        $value     = null;
        $this->hit = false;

        $inMemoryValue = $this->inMemoryAdapter->get($key);
        if ($this->inMemoryAdapter->isHit()) {
            $this->hit = true;
            return $inMemoryValue;
        }

        $fileKey = $this->getFilenameFromCacheKey($key);

        if (true === file_exists($fileKey)) {
            $value = $this->restoreDataStructure(file_get_contents($fileKey));
            if ($value['expires'] >= (new DateTime())) {
                $this->hit = true;
                return $value['value'];
            }

            $this->removeCacheFile($fileKey);
        }

        return (null !== $this->nextAdapter) ? $this->nextAdapter->get($key) : null;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getFilenameFromCacheKey($key)
    {
        return $this->cacheDir
        . DIRECTORY_SEPARATOR
        . $this->getDirectoryHash($key)
        . DIRECTORY_SEPARATOR
        . $key
        . self::CACHE_FILE_SUFFIX;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getDirectoryHash($key)
    {
        $level1 = substr($key, 0, 1);

        $level2 = $level1;
        if (true === strlen($key) > 1) {
            $level2 = substr($key, 1, 1);
        }

        $level3 = $level1;
        if (true === strlen($key) > 2) {
            $level3 = substr($key, 2, 1);
        }

        $directoryHash = $level1 . DIRECTORY_SEPARATOR . $level2 . DIRECTORY_SEPARATOR . $level3;
        $this->createCacheHashDirectory($directoryHash);

        return $directoryHash;
    }

    /**
     * @param $directoryHash
     */
    private function createCacheHashDirectory($directoryHash)
    {
        $cacheDir = $this->cacheDir . DIRECTORY_SEPARATOR . $directoryHash;
        if (false === file_exists($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
    }

    /**
     * @param $fileKey
     *
     * @throws \RuntimeException
     */
    private function removeCacheFile($fileKey)
    {
        unlink($fileKey);
    }

    /**
     * Set a value identified by $key and with an optional $ttl.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl
     *
     * @return $this
     */
    public function set($key, $value, $ttl = 0)
    {
        $ttl = $this->fromDefaultTtl($ttl);

        if ($ttl >= 0) {
            $calculatedTtl = $this->getCalculatedTtl($ttl);
            $data          = $this->buildDataCache($value, $calculatedTtl);
            file_put_contents($this->getFilenameFromCacheKey($key), $data);

            $this->inMemoryAdapter->set($key, $value, $ttl);

            if (null !== $this->nextAdapter) {
                $this->nextAdapter->set($key, $value, $ttl);
            }
        }

        return $this;
    }

    /**
     * @param $ttl
     *
     * @return int
     */
    private function getCalculatedTtl($ttl)
    {
        $calculatedTtl = strtotime(sprintf('now +%s seconds', $ttl));

        if (0 == $ttl) {
            $calculatedTtl = strtotime('now +10 years');
        }
        return $calculatedTtl;
    }

    /**
     * @param $value
     * @param $calculatedTtl
     *
     * @return string
     */
    private function buildDataCache($value, $calculatedTtl)
    {
        return $this->storageDataStructure(
            [
                'value'   => $value,
                'expires' => new DateTime(date('Y-m-d H:i:s', $calculatedTtl))
            ]
        );
    }

    /**
     * Delete a value identified by $key.
     *
     * @param  string $key
     *
     * @throws \RuntimeException
     */
    public function delete($key)
    {
        $fileKey = $this->getFilenameFromCacheKey($key);

        if (true === file_exists($fileKey)) {
            $this->removeCacheFile($fileKey);
        }

        $this->inMemoryAdapter->delete($key);

        if (null !== $this->nextAdapter) {
            $this->nextAdapter->delete($key);
        }
    }

    /**
     * Checks the availability of the cache service.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return file_exists($this->cacheDir) && is_dir($this->cacheDir) && is_writable($this->cacheDir);
    }

    /**
     * Clears all expired values from cache.
     *
     * @return mixed
     */
    public function clear()
    {
        $this->clearCacheFiles($this->cacheDir);
        $this->inMemoryAdapter->clear();

        if (null !== $this->nextAdapter) {
            $this->nextAdapter->clear();
        }
    }

    /**
     * @param string $directory
     */
    private function clearCacheFiles($directory)
    {
        foreach (glob("{$directory}/*") as $file) {
            if (is_dir($file)) {
                $this->clearCacheFiles($file);
            } else {
                $value = $this->restoreDataStructure(file_get_contents($file));
                if ($value['expires'] < (new DateTime())) {
                    $this->removeCacheFile($file);
                }
            }
        }
    }

    /**
     * Clears all values from the cache.
     *
     * @return mixed
     */
    public function drop()
    {
        $this->removeCacheFiles($this->cacheDir);
        $this->inMemoryAdapter->drop();

        if (null !== $this->nextAdapter) {
            $this->nextAdapter->drop();
        }
    }

    /**
     * @param string $directory
     */
    private function removeCacheFiles($directory)
    {
        foreach (glob("{$directory}/*") as $file) {
            if (is_dir($file)) {
                $this->removeCacheFiles($file);
            } else {
                $this->removeCacheFile($file);
            }
        }
    }
}
