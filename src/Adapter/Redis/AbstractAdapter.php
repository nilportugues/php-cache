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
     * @param CacheAdapter    $next
     */
    abstract public function __construct(array $connections, CacheAdapter $next = null);

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

        $inMemoryValue = InMemoryAdapter::getInstance()->get($key);

        if (InMemoryAdapter::getInstance()->isHit()) {
            $this->hit = true;
            return $inMemoryValue;
        }

        if ($this->isAvailable()) {
            $value = $this->redis->get($key);

            if ($value) {
                $this->hit = true;
                $value     = $this->restoreDataStructure($value);
                InMemoryAdapter::getInstance()->set($key, $value, 0);
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
        $this->redis->del($key);
        $this->deleteChain($key);
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
        $this->redis->flushDB();
        $this->dropChain();
    }
}
