<?php

namespace NilPortugues\Cache\Adapter;

use InvalidArgumentException;
use NilPortugues\Cache\Adapter\ElasticSearch\Curl;
use NilPortugues\Cache\CacheAdapter;

/**
 * Class ElasticSearchAdapter
 * @package NilPortugues\Cache\Adapter
 */
class ElasticSearchAdapter extends Adapter implements CacheAdapter
{
    /**
     * @var \NilPortugues\Cache\Adapter\ElasticSearch\Curl
     */
    private $curl;

    /**
     * @var string
     */
    private $base = '';

    /**
     * @var string
     */
    private $baseUrl = '';

    /**
     * @var array
     */
    private $createCache = [
        "mappings" => [
            "cache" => [
                "_source"    => [
                    "enabled" => false
                ],
                "_ttl"       => [
                    "enabled" => true,
                    "default" => "1000ms"
                ],
                "properties" => [
                    "value" => [
                        "type"  => "string",
                        "index" => "not_analyzed"
                    ]
                ]
            ]
        ]
    ];

    /**
     * @param string          $baseUrl
     * @param string          $indexName
     * @param InMemoryAdapter $inMemory
     * @param CacheAdapter    $next
     *
     * @throws InvalidArgumentException
     */
    public function __construct($baseUrl, $indexName, InMemoryAdapter $inMemory, CacheAdapter $next = null)
    {
        $baseUrl   = (string)$baseUrl;
        $indexName = (string)$indexName;
        $this->curl = $this->getCurlClient();

        $this->base    = \sprintf("%s/%s", $baseUrl, $indexName);
        $this->baseUrl = \sprintf("%s/%s/cache", $baseUrl, $indexName);

        if (false === \filter_var($this->base, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('The provided base URL is not a valid URL');
        }

        if (false === $this->curl->cacheIndexExists()) {
            $this->curl->createCacheIndex($this->base, $this->createCache);
        }

        $this->inMemoryAdapter = $inMemory;
        $this->nextAdapter     = ($inMemory === $next) ? null : $next;
    }

    /**
     * @codeCoverageIgnore
     * @return Curl
     */
    protected function getCurlClient()
    {
        return new Curl($this->base, $this->baseUrl);
    }

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
        $this->hit = false;

        $inMemoryValue = $this->inMemoryAdapter->get($key);
        if ($this->inMemoryAdapter->isHit()) {
            $this->hit = true;
            return $inMemoryValue;
        }

        $value = $this->curl->get($key);

        if (null !== $value) {
            $this->hit = true;
            $this->inMemoryAdapter->set($key, $value, 0);
            return $this->restoreDataStructure($value);
        }

        return (null !== $this->nextAdapter) ? $this->nextAdapter->get($key) : null;
    }

    /**
     * Delete a value identified by $key.
     *
     * @param  string $key
     */
    public function delete($key)
    {
        $this->curl->delete($key);
        $this->deleteChain($key);
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
        $ttl = $this->fromDefaultTtl($ttl);

        if ($ttl >= 0) {
            $response = $this->curl->set($key, $this->storageDataStructure($value), $ttl);

            if (false !== $response) {
                $response = \json_decode($response, true);

                if (\array_key_exists('ok', $response) && true === $response['ok']) {
                    $this->setChain($key, $value, $ttl);
                }
            }
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
        return $this->curl->cacheIndexExists();
    }

    /**
     * Clears all expired values from cache.
     *
     * @return mixed
     */
    public function clear()
    {
        $this->clearChain();
    }

    /**
     * Clears all values from the cache.
     *
     * @return mixed
     */
    public function drop()
    {
        $this->curl->drop($this->base);
        $this->curl->createCacheIndex($this->base, $this->createCache);
        $this->dropChain();
    }
}
