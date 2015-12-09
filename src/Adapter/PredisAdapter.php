<?php

namespace NilPortugues\Cache\Adapter;

use NilPortugues\Cache\Adapter\Redis\AbstractAdapter;
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
     * @param CacheAdapter    $next
     */
    public function __construct(array $connections, CacheAdapter $next = null)
    {
        $this->nextAdapter     = (InMemoryAdapter::getInstance() === $next) ? null: $next;

        try {
            $this->connected = true;
            $this->redis = new Client($connections);
            $this->redis->connect();
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
