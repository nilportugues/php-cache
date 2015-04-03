<?php

namespace NilPortugues\Cache\Adapter;

use DateTime;
use NilPortugues\Cache\CacheAdapter;

/**
 * Class FileSystemAdapter
 * @package NilPortugues\Cache\Adapter
 */
class FileSystemAdapter extends Adapter implements CacheAdapter
{
    const CACHE_FILE_PREFIX = '__';
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
     */
    public function __construct($cacheDir, InMemoryAdapter $inMemory, CacheAdapter $next = null)
    {
        $this->inMemoryAdapter = $inMemory;
        $this->nextAdapter     = $next;

        $cacheDir = realpath($cacheDir);

        if (false === is_dir($cacheDir)) {
        }

        if (false === is_writable($cacheDir)) {
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
        $key = (string)$key;

        $inMemoryValue = $this->inMemoryAdapter->get($key);
        if ($this->inMemoryAdapter->isHit()) {
            $this->hit = true;
            return $inMemoryValue;
        }

        $fileKey = $this->getFilenameFromCacheKey($key);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getFilenameFromCacheKey($key)
    {
        return $this->cacheDir . DIRECTORY_SEPARATOR . self::CACHE_FILE_PREFIX . $key . self::CACHE_FILE_SUFFIX;
    }

    /**
     * Set a value identified by $key and with an optional $ttl.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl
     *
     * @throws \RuntimeException
     * @return $this
     */
    public function set($key, $value, $ttl = 0)
    {
        $ttl = $this->fromDefaultTtl($ttl);

        if ($ttl >= 0) {
            $calculatedTtl = $this->getCalculatedTtl($ttl);
            $data          = $this->buildDataCache($value, $calculatedTtl);

            if (false === file_put_contents($this->getFilenameFromCacheKey($key), $data)) {
                throw new \RuntimeException(
                    sprintf('Could not persist to file system cache the value associated with key: %s', $key)
                );
            }

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
     */
    public function delete($key)
    {
        $this->inMemoryAdapter->delete($key);
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
        $this->inMemoryAdapter->clear();
    }

    /**
     * Clears all values from the cache.
     *
     * @return mixed
     */
    public function drop()
    {
        $this->inMemoryAdapter->drop();
    }
}
