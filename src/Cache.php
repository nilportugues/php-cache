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
}
