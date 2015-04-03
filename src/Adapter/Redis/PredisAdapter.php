<?php

namespace NilPortugues\Cache\Adapter\Redis;

use NilPortugues\Cache\Adapter\Adapter;
use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\CacheAdapter;
use Predis\Client;

/**
 * Class PredisAdapter
 * @package NilPortugues\Cache\Adapter\Redis
 */
class PredisAdapter extends Adapter implements CacheAdapter
{
    /**
     * @var CacheAdapter|null
     */
    private $nextAdapter;

    /**
     * Predis client instance
     *
     * @var \Predis\Client
     */
    private $redis;

    /**
     * @var bool
     */
    private $connected;

    /**
     * @var InMemoryAdapter
     */
    private $inMemoryAdapter;

    /**
     * @param array           $connections
     * @param InMemoryAdapter $inMemory
     * @param CacheAdapter    $next
     */
    public function __construct(array $connections, InMemoryAdapter $inMemory, CacheAdapter $next = null)
    {
        $this->inMemoryAdapter = $inMemory;
        $this->nextAdapter     = $next;

        try {
            $this->redis = new Client($connections);
            $this->redis->connect();
            $this->connected = true;
        } catch (\Exception $e) {
            $this->connected = false;
        }
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
        $this->hit = false;

        $inMemoryValue = $this->inMemoryAdapter->get($key);
        if ($this->inMemoryAdapter->isHit()) {
            $this->hit = true;
            return $inMemoryValue;
        }

        if ($this->isAvailable()) {
            $value = $this->redis->get($key);
            if ($value) {
                $this->hit = true;
                $value = $this->restoreDataStructure($value);
                $this->inMemoryAdapter->set($key, $value);
                return $value;
            }
        }

        return (null !== $this->nextAdapter) ? $this->nextAdapter->get($key) : null;
    }

    /**
     * Checks the availability of the cache service.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->connected && $this->redis->isConnected();
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
            if ($this->isAvailable()) {
                $this->redis->set($key, $this->storageDataStructure($value));

                if ($ttl > 0) {
                    $this->redis->expire($key, $ttl);
                }
            }

            $this->inMemoryAdapter->set($key, $value);
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
        $this->redis->del($key);

        if (null !== $this->nextAdapter) {
            $this->nextAdapter->delete($key);
        }
    }

    /**
     * Clears all expired values from cache.
     *
     * @return mixed
     */
    public function clear()
    {
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
        $this->redis->flushDB();

        if (null !== $this->nextAdapter) {
            $this->nextAdapter->drop();
        }
    }
}
