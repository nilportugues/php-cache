<?php
namespace NilPortugues\Cache;

interface CacheAdapter
{
    /**
     * Get a value identified by $key.
     *
     * @param  string     $key
     * @return bool|mixed
     */
    public function get($key);

    /**
     * Set a value identified by $key and with an optional $ttl.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl
     * @return $this
     */
    public function set($key, $value, $ttl = 0);

    /**
     * Allows to set a default ttl value if none is provided for set()
     *
     * @param  int     $ttl
     * @return bool|mixed
     */
    public function defaultTtl($ttl);
    
    /**
     * Delete a value identified by $key.
     *
     * @param  string     $key
     */
    public function delete($key);

    /**
     * Checks the availability of the cache service.
     *
     * @return bool
     */
    public function isAvailable();

    /**
     * Check if value was found in the cache or not.
     *
     * @return bool
     */
    public function isHit();

    /**
     * Clears all expired values from cache.
     *
     * @return mixed
     */
    public function clear();

    /**
     * Clears all values from the cache.
     *
     * @return mixed
     */
    public function drop();
}
