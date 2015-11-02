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

        if (\array_key_exists($key, $this->registry)) {
            if ($this->registry[$key]['expires'] >= (new DateTime())) {
                $this->hit = true;
                return $this->restoreDataStructure($key);
            }
            unset($this->registry[$key]);
        }

        return $value;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    protected function restoreDataStructure($key)
    {
        if ($this->isSerializedArray($key)) {
            return \unserialize($this->registry[$key]['value']);
        }

        return (\is_object($this->registry[$key]['value'])) ?
            clone $this->registry[$key]['value'] :
            $this->registry[$key]['value'];
    }

    /**
     * @param $key
     *
     * @return bool
     */
    private function isSerializedArray($key)
    {
        return \is_string($this->registry[$key]['value'])
        && 'a:' === \substr($this->registry[$key]['value'], 0, 2)
        && '}' === \substr($this->registry[$key]['value'], -1)
        && (':{i:' === \substr($this->registry[$key]['value'], 3, 4)
            || ':{s:' === \substr($this->registry[$key]['value'], 3, 4));
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
        $ttl = $this->fromDefaultTtl($ttl);

        if ($ttl >= 0) {
            $calculatedTtl = $this->getCalculatedTtl($ttl);

            $this->registry[$key] = [
                'value'   => $this->storageDataStructure($value),
                'expires' => new DateTime(\date('Y-m-d H:i:s', $calculatedTtl))
            ];
        }
        return $this;
    }

    /**
     * @param $ttl
     *
     * @return int
     */
    private function getCalculatedTtl($ttl)
    {
        $calculatedTtl = \strtotime(\sprintf('now +%s seconds', $ttl));
        if (0 == $ttl) {
            $calculatedTtl = \strtotime('now +10 years');
        }
        return $calculatedTtl;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function storageDataStructure($value)
    {
        if (\is_array($value)) {
            return \serialize($value);
        }

        return (\is_object($value)) ? clone $value : $value;
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

        if (\array_key_exists($key, $this->registry)) {
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
        foreach (\array_keys($this->registry) as $key) {
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
