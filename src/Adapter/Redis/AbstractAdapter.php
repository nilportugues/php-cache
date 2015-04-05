<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/3/15
 * Time: 6:01 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Cache\Adapter\Redis;

use NilPortugues\Cache\Adapter\Adapter;
use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\CacheAdapter;

/**
 * Class AbstractAdapter
 * @package NilPortugues\Cache\Adapter\Redis
 */
abstract class AbstractAdapter extends Adapter implements CacheAdapter
{
    /**
     * @var CacheAdapter|null
     */
    protected $nextAdapter;

    /**
     * Redis client instance
     *
     * @var \Redis|\Predis\Client
     */
    protected $redis;

    /**
     * @var bool
     */
    protected $connected;

    /**
     * @var InMemoryAdapter
     */
    protected $inMemoryAdapter;

    /**
     * @param array           $connections
     * @param InMemoryAdapter $inMemory
     * @param CacheAdapter    $next
     */
    abstract public function __construct(array $connections, InMemoryAdapter $inMemory, CacheAdapter $next = null);

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
                $value     = $this->restoreDataStructure($value);
                $this->inMemoryAdapter->set($key, $value, 0);
                return $value;
            }
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
            if ($this->isAvailable()) {
                $this->redis->set($key, $this->storageDataStructure($value));

                if ($ttl > 0) {
                    $this->redis->expire($key, $ttl);
                }
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
        $this->redis->del($key);
        $this->inMemoryAdapter->delete($key);

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
        $this->redis->flushDB();
        $this->inMemoryAdapter->drop();

        if (null !== $this->nextAdapter) {
            $this->nextAdapter->drop();
        }
    }
}
