<?php

namespace NilPortugues\Cache\Adapter\Redis;

use NilPortugues\Cache\CacheAdapter;
use NilPortugues\Cache\Adapter\Adapter;

/**
 * Class NativeAdapter
 * @package NilPortugues\Cache\Adapter\Redis
 */
class NativeAdapter extends Adapter  implements CacheAdapter
{
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
