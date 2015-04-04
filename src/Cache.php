<?php
namespace NilPortugues\Cache;

use NilPortugues\Cache\Adapter\InMemoryAdapter;

/**
 *
 */
final class Cache implements CacheAdapter
{
    /**
    * @var CacheAdapter
    */
    private $cache;
    
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var int
     */
    private $expires;

    /**
     * @param CacheAdapter $cache
     * @param string $namespace
     * @param int $expires
     */
    public function __construct(CacheAdapter $cache = null, $namespace = '', $expires = 0)
    {
        $this->cache = (null === $cache) ? new InMemoryAdapter() : $cache;
        $this->namespace = (empty($namespace)) ? '' : $namespace.".";
        $this->expires = (int) $expires;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->cache->get($this->namespace.$key);
    }

    /**
     * @param string $key
     * @param int $value
     * @param null $ttl
     * @return $this
     */
    public function set($key, $value, $ttl = null)
    {
        $ttl = (null === $ttl) ? $this->expires : $ttl;
        $this->cache->set($this->namespace.$key, $value, $ttl);
        return $this;
    }

    /**
     * Allows to set a default ttl value if none is provided for set()
     *
     * @param  int $ttl
     *
     * @return bool|mixed
     */
    public function defaultTtl($ttl)
    {
        // TODO: Implement defaultTtl() method.
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
        // TODO: Implement isAvailable() method.
    }

    /**
     * Check if value was found in the cache or not.
     *
     * @return bool
     */
    public function isHit()
    {
        // TODO: Implement isHit() method.
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
