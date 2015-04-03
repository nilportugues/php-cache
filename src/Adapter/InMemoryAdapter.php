<?php

namespace NilPortugues\Cache\Adapter;

use DateTime;
use NilPortugues\Cache\CacheAdapter;

/**
 * Class InMemoryAdapter
 * @package NilPortugues\Cache\Adapter
 */
class InMemoryAdapter extends Adapter implements CacheAdapter
{

    /**
     * @var array
     */
    private $registry = [];


    /**
     * Get a value identified by $key.
     *
     * @param  string $key
     *
     * @return bool|mixed
     */
    public function get($key)
    {
        $key       = (string)$key;
        $value     = null;
        $this->hit = false;

        if (array_key_exists($key, $this->registry)) {
            if ($this->registry[$key]['expires'] >= (new DateTime())) {
                $this->hit = true;
                $value     = $this->registry[$key]['value'];
            } else {
                unset($this->registry[$key]);
            }
        }

        return (is_object($value)) ? clone $value : $value;
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
        $key = (string)$key;
        $ttl = (int)$ttl;

        if (0 == $ttl && null !== $this->ttl) {
            $ttl = $this->ttl;
        }

        if ($ttl >= 0) {
            $calculatedTtl = strtotime(sprintf('now +%s seconds', $ttl));
            if (0 == $ttl) {
                $calculatedTtl = strtotime('now +10 years');
            }

            $this->registry[$key] = [
                'value'   => (is_object($value)) ? clone $value : $value,
                'expires' => new DateTime(date('Y-m-d H:i:s', $calculatedTtl))
            ];
        }
        return $this;
    }

    /**
     * Delete a value identified by $key.
     *
     * @param string $key
     *
     * @return $this
     */
    public function delete($key)
    {
        $key = (string)$key;

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
     * Clears all expired values from cache.
     *
     * @return mixed
     */
    public function clear()
    {
        $currentDate = new DateTime();
        foreach (array_keys($this->registry) as $key) {
            $this->clearExpiredKey($key, $currentDate);
        }
    }

    /**
     * Clear an item if it expired.
     *
     * @param          $key
     * @param DateTime $dateTime
     */
    private function clearExpiredKey($key, DateTime $dateTime)
    {
        if ($this->registry[$key]['expires'] < $dateTime) {
            unset($this->registry[$key]);
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
