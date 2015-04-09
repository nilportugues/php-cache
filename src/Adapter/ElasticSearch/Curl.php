<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/6/15
 * Time: 6:30 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Cache\Adapter\ElasticSearch;

/**
 * Class Curl
 * @package NilPortugues\Cache\Adapter\ElasticSearch
 */
class Curl implements CurlClient
{
    /**
     * @var string
     */
    private $base;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @param string $base
     * @param string $baseUrl
     */
    public function __construct($base, $baseUrl)
    {
        $this->base = $base;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return bool
     */
    public function cacheIndexExists()
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
     * @param       $base
     * @param array $createCache
     * @return void
     */
    public function createCacheIndex($base, array $createCache)
    {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_URL, $base);
        curl_setopt($curlHandler, CURLOPT_POST, true);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, json_encode($createCache));
        curl_exec($curlHandler);
    }


    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        $curlHandler = $this->curlHandler($key.'?fields=_source,_ttl');

        $response = curl_exec($curlHandler);
        curl_close($curlHandler);

        if (false !== $response) {
            $response = json_decode($response, true);

            if (true === $response['exists'] && $response['fields']['_ttl'] > 0) {
                return $response["_source"]['value'];
            }
        }

        return null;
    }


    /**
     * @param string $key
     *
     * @return resource
     */
    public function curlHandler($key)
    {
        $curlHandler = curl_init();

        curl_setopt($curlHandler, CURLOPT_URL, sprintf("{$this->baseUrl}/%s", $key));
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);

        return $curlHandler;
    }

    /**
     * @param $key
     */
    public function delete($key)
    {
        $curlHandler = $this->curlHandler($key);
        curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, "DELETE");

        curl_exec($curlHandler);
        curl_close($curlHandler);
    }


    /**
     * @param $key
     * @param $value
     * @param $ttl
     *
     * @return mixed
     */
    public function set($key, $value, $ttl)
    {
        $curlHandler = $this->curlHandler($key . '?ttl=' . $ttl . 's');

        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_POST, true);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $value);

        $response = curl_exec($curlHandler);
        curl_close($curlHandler);
        return $response;
    }


    /**
     *
     */
    public function drop($base)
    {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_URL, $base);
        curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_exec($curlHandler);
    }
}
