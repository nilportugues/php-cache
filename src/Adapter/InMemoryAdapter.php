<?php

namespace NilPortugues\Cache\Adapter;

use NilPortugues\Cache\CacheAdapter;

/**
 * Class InMemoryAdapter
 * @package NilPortugues\Cache\Adapter
 */
class InMemoryAdapter implements CacheAdapter
{
    /**
     * @var CacheAdapter|null
     */
    private $next;

    /**
     * @var bool
     */
    private $hit = false;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * Get a value identified by $key.
     *
     * @param  string $key
     * @return bool|mixed
     */
    public function get($key)
    {
        $value = null;
        $this->hit = false;

        if (array_key_exists($key, $this->registry)
            && array_key_exists('expires', $this->registry[$key])
            && $this->registry[$key]['expires'] >= strtotime('now')
        ) {
            $value = $this->registry[$key]['value'];
        }

        $this->clearExpiredKey($key);

        $this->hit = (null !== $value);

        return (is_object($value)) ? clone $value : $value;
    }

    /**
     * Clear an item if it expired.
     * @param $key
     */
    private function clearExpiredKey($key)
    {
        if ($this->registry[$key]['expires'] < strtotime('now')) {
            unset($this->registry[$key]);
        }
    }

    /**
     * Set a value identified by $key and with an optional $ttl.
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return $this
     */
    public function set($key, $value, $ttl = 0)
    {
        $ttl = (0 == $ttl) ? PHP_INT_MAX: $ttl;
        $value = (is_object($value)) ? clone $value : $value;

        $this->registry[$key] = [
            'value' => $value,
            'expires' => strtotime(sprintf('now +%s seconds', $ttl))
        ];

        return $this;
    }

    /**
     * Delete a value identified by $key.
     *
     * @param string $key
     * @return $this
     */
    public function delete($key)
    {
        if (array_key_exists($key, $this->registry)) {
            unset($this->registry[$key]);
        }
        return $this;
    }

    /**
     * Checks the availability of the cache service.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * Check if value was found in the cache or not.
     *
     * @return bool
     */
    public function isHit()
    {
        return $this->hit;
    }

    /**
     * Clears all expired values from cache.
     *
     * @return mixed
     */
    public function clear()
    {
        foreach (array_keys($this->registry) as $key) {
            $this->clearExpiredKey($key);
        }
    }

    /**
     * Clears all values from the cache.
     *
     * @return mixed
     */
    public function drop()
    {
        $this->registry = [];
    }
}
