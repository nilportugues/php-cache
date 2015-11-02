<?php

namespace NilPortugues\Cache\Adapter;

use NilPortugues\Cache\Adapter\Redis\AbstractAdapter;
use NilPortugues\Cache\CacheAdapter;
use Redis;
use RedisException;

/**
 * Class RedisAdapter
 * @package NilPortugues\Cache\Adapter\Redis
 */
class RedisAdapter extends AbstractAdapter
{
    /**
     * @param array           $connections
     * @param InMemoryAdapter $inMemory
     * @param CacheAdapter    $next
     *
     * @throws \Exception
     */
    public function __construct(array $connections, InMemoryAdapter $inMemory, CacheAdapter $next = null)
    {
        $this->isRedisExtensionAvailable();

        try {
            $connections = \array_values($connections);
            $this->connected = true;

            $this->redis = new Redis();
            $this->redis->connect($connections[0]['host'], $connections[0]['port'], $connections[0]['timeout']);
            $this->redis->select($connections[0]['database']);
        } catch (RedisException $e) {
            $this->connected = false;
        }

        $this->inMemoryAdapter = $inMemory;
        $this->nextAdapter     = ($inMemory === $next) ? null: $next;
    }


    /**
     * Checks the availability of the cache service.
     *
     * @return bool
     */
    public function isAvailable()
    {
        $available = true;
        try {
            $this->redis->ping();
        } catch (RedisException $e) {
            $available = false;
        }

        return $available;
    }

    /**
     * @throws \Exception
     * @codeCoverageIgnore
     */
    private function isRedisExtensionAvailable()
    {
        if (false === \class_exists('\Redis')) {
            throw new \Exception(
                \sprintf(
                    'Redis extension for PHP is not installed on the system, use %s class instead.',
                    '\NilPortugues\Cache\Adapter\PredisAdapter'
                )
            );
        }
    }
}
