<?php

namespace NilPortugues\Cache\Adapter;

use Memcached;
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
    private $memcached;

    /**
     * @param string $persistentId
     * @param array           $connections
     * @param InMemoryAdapter $inMemory
     * @param CacheAdapter    $next
     */
    public function __construct($persistentId, array $connections, InMemoryAdapter $inMemory, CacheAdapter $next = null)
    {
        $this->inMemoryAdapter = $inMemory;
        $this->nextAdapter     = ($inMemory === $next) ? null: $next;

        $connections = array_unique($connections);
        $this->memcached = new Memcached($persistentId);
        $this->memcached->addServers($connections);

        $this->memcached->setOption(Memcached::OPT_SERIALIZER,
            ($this->memcached->getOption(Memcached::HAVE_IGBINARY))
                ? Memcached::SERIALIZER_IGBINARY : Memcached::SERIALIZER_PHP
        );

        $this->memcached->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
        $this->memcached->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        $this->memcached->setOption(Memcached::OPT_NO_BLOCK, true);
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
        // TODO: Implement get() method.
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
        // TODO: Implement set() method.
    }

    /**
     * Delete a value identified by $key.
     *
     * @param  string $key
     */
    public function delete($key)
    {
        // TODO: Implement delete() method.
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
        // TODO: Implement clear() method.
    }

    /**
     * Clears all values from the cache.
     *
     * @return mixed
     */
    public function drop()
    {
        // TODO: Implement drop() method.
    }
}
