<?php

namespace NilPortugues\Cache\Adapter;

use NilPortugues\Cache\Adapter\Memcached\Memcached;
use NilPortugues\Cache\CacheAdapter;

/**
 * Class MemcachedAdapter
 * @package NilPortugues\Cache\Adapter
 */
class MemcachedAdapter extends Adapter implements CacheAdapter
{
    /**
     * @var Memcached
     */
    protected $memcached;

    /**
     * @param string          $persistentId
     * @param array           $connections
     * @param InMemoryAdapter $inMemory
     * @param CacheAdapter    $next
     */
    public function __construct($persistentId, array $connections, InMemoryAdapter $inMemory, CacheAdapter $next = null)
    {
        $this->memcached       = $this->getMemcachedClient($persistentId, array_unique(array_values($connections)));
        $this->inMemoryAdapter = $inMemory;
        $this->nextAdapter     = ($inMemory === $next) ? null : $next;
    }

    /**
     * @param string $persistentId
     * @param array $connections
     *
     * @codeCoverageIgnore
     * @return Memcached
     */
    protected function getMemcachedClient($persistentId, array $connections)
    {
        return new Memcached($persistentId, $connections);
    }

    /**
     * Get a value identified by $key.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $this->hit = false;

        $inMemoryValue = $this->inMemoryAdapter->get($key);

        if ($this->inMemoryAdapter->isHit()) {
            $this->hit = true;
            return $inMemoryValue;
        }

        $value = $this->memcached->get($key);

        if ($value) {
            $this->hit = true;
            $this->inMemoryAdapter->set($key, $value, 0);
            return $value;
        }

        return (null !== $this->nextAdapter) ? $this->nextAdapter->get($key) : null;
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
            $this->memcached->set($key, $value);

            if ($ttl > 0) {
                $this->memcached->touch($key, time() + $ttl);
            }

            $this->setChain($key, $value, $ttl);
        }

        return $this;
    }

    /**
     * Delete a value identified by $key.
     *
     * @param  string $key
     */
    public function delete($key)
    {
        $this->memcached->delete($key);
        $this->deleteChain($key);
    }

    /**
     * Checks the availability of the cache service.
     *
     * @return bool
     */
    public function isAvailable()
    {
        $stats = $this->memcached->getStats();
        return false === empty($stats);
    }

    /**
     * Clears all expired values from cache.
     *
     * @return mixed
     */
    public function clear()
    {
        $this->clearChain();
    }

    /**
     * Clears all values from the cache.
     *
     * @return mixed
     */
    public function drop()
    {
        $this->memcached->flush();
        $this->dropChain();
    }
}
