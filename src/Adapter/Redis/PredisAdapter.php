<?php

namespace NilPortugues\Cache\Adapter\Redis;

use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\CacheAdapter;
use Predis\Client;

/**
 * Class PredisAdapter
 * @package NilPortugues\Cache\Adapter\Redis
 */
class PredisAdapter extends AbstractAdapter
{
    /**
     * @param array           $connections
     * @param InMemoryAdapter $inMemory
     * @param CacheAdapter    $next
     */
    public function __construct(array $connections, InMemoryAdapter $inMemory, CacheAdapter $next = null)
    {
        $this->inMemoryAdapter = $inMemory;
        $this->nextAdapter     = ($inMemory === $next) ? null: $next;

        try {
            $this->redis = new Client($connections);
            $this->redis->connect();
            $this->connected = true;
        } catch (\Exception $e) {
            $this->connected = false;
        }
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
}
