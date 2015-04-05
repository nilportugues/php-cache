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
     * @param string          $persistentId
     * @param array           $connections
     * @param InMemoryAdapter $inMemory
     * @param CacheAdapter    $next
     */
    public function __construct($persistentId, array $connections, InMemoryAdapter $inMemory, CacheAdapter $next = null)
    {
        $this->inMemoryAdapter = $inMemory;
        $this->nextAdapter     = ($inMemory === $next) ? null : $next;

        $connections     = array_values($connections);
        $connections     = array_unique($connections);
        $this->memcached = new Memcached($persistentId);
        $this->memcached->addServers($connections);

        $this->memcached->setOption(
            Memcached::OPT_SERIALIZER,
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

            if ($ttl>0) {
                $this->memcached->touch($key, time() + $ttl);
            }

            $this->inMemoryAdapter->set($key, $value, $ttl);
            if (null !== $this->nextAdapter) {
                $this->nextAdapter->set($key, $value, $ttl);
            }
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
        $keys = $this->memcached->getAllKeys();
        $this->memcached->deleteMulti(is_array($keys) ? $keys : [], time());

        $this->inMemoryAdapter->clear();

        if (null !== $this->nextAdapter) {
            $this->nextAdapter->clear();
        }
    }

    /**
     * Clears all values from the cache.
     *
     * @return mixed
     */
    public function drop()
    {
        $this->memcached->flush();
        $this->inMemoryAdapter->drop();

        if (null !== $this->nextAdapter) {
            $this->nextAdapter->drop();
        }
    }
}
