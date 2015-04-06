<?php

namespace NilPortugues\Cache\Adapter;

use InvalidArgumentException;
use NilPortugues\Cache\CacheAdapter;

/**
 * Class ElasticSearchAdapter
 * @package NilPortugues\Cache\Adapter
 */
class ElasticSearchAdapter extends Adapter implements CacheAdapter
{
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

        $this->base    = sprintf("%s/%s", $baseUrl, $indexName);
        $this->baseUrl = sprintf("%s/%s/cache", $baseUrl, $indexName);

        if (false === filter_var($this->base, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('The provided base URL is not a valid URL');
        }

        if (false === $this->curlCacheIndexExists()) {
            $this->curlCreateCacheIndex();
        }
    }

    /**
     * @return bool
     */
    private function curlCacheIndexExists()
    {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_URL, sprintf("{$this->base}/%s", '_settings'));
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curlHandler);

        if (false !== $response) {
            $response = json_decode($response, true);
            return array_key_exists('index_name', $response);
        }

        return false;
    }

    /**
     *
     */
    private function curlCreateCacheIndex()
    {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_URL, $this->base);
        curl_setopt($curlHandler, CURLOPT_POST, true);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, json_encode($this->createCache));
        curl_exec($curlHandler);
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

        $value = $this->curlGet($key);

        if (null !== $value) {
            $this->hit = true;
            $this->inMemoryAdapter->set($key, $value, 0);
            return $value;
        }

        return (null !== $this->nextAdapter) ? $this->nextAdapter->get($key) : null;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    private function curlGet($key)
    {
        $curlHandler = $this->curlHandler($key, '?fields=_source,_ttl');

        $response = curl_exec($curlHandler);
        curl_close($curlHandler);

        if (false !== $response) {
            $response = json_decode($response, true);

            if (true == $response['exists'] && $response['fields']['_ttl'] > 0) {
                return $this->restoreDataStructure($response["_source"]['value']);
            }
            $this->delete($key);
        }

        return null;
    }

    /**
     * @param string $key
     *
     * @return resource
     */
    private function curlHandler($key)
    {
        $curlHandler = curl_init();

        curl_setopt($curlHandler, CURLOPT_URL, sprintf("{$this->baseUrl}/%s", $key));
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);

        return $curlHandler;
    }

    /**
     * Delete a value identified by $key.
     *
     * @param  string $key
     */
    public function delete($key)
    {
        $this->curlDelete($key);
        $this->deleteChain($key);
    }

    /**
     * @param $key
     */
    private function curlDelete($key)
    {
        $curlHandler = $this->curlHandler($key);
        curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, "DELETE");

        curl_exec($curlHandler);
        curl_close($curlHandler);
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
            $response = $this->curlSet($key, $value, $ttl);

            if (false !== $response) {
                $response = json_decode($response, true);

                if (array_key_exists('ok', $response) && true == $response['ok']) {
                    $this->setChain($key, $value, $ttl);
                }
            }

        }

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @param $ttl
     *
     * @return mixed
     */
    private function curlSet($key, $value, $ttl)
    {
        $curlHandler = $this->curlHandler($key . '?ttl=' . $ttl . 's');

        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_POST, true);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, ['value' => $this->storageDataStructure($value)]);

        $response = curl_exec($curlHandler);
        curl_close($curlHandler);
        return $response;
    }

    /**
     * Checks the availability of the cache service.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->curlCacheIndexExists();
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
        $this->curlDrop();
        $this->curlCreateCacheIndex();
        $this->dropChain();
    }

    /**
     *
     */
    private function curlDrop()
    {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_URL, $this->base);
        curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_exec($curlHandler);
    }
}
