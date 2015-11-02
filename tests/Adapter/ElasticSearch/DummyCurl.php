<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/6/15
 * Time: 8:53 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\ElasticSearch;

use DateTime;
use NilPortugues\Cache\Adapter\ElasticSearch\CurlClient;

/**
 * Class DummyCurl
 * @package NilPortugues\Tests\Cache\Adapter\ElasticSearch
 */
class DummyCurl implements CurlClient
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * @return mixed
     */
    public function cacheIndexExists()
    {
        return false;
    }

    /**
     * @param       $base
     * @param array $createCache
     *
     * @return mixed
     */
    public function createCacheIndex($base, array $createCache)
    {
        $this->registry = $this->registry = [
            'already.cached.value' => [
                'value' => \serialize(1),
                'ttl' => new DateTime()
            ]
        ];
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (true === \array_key_exists($key, $this->registry)) {
            if ($this->registry[$key]['ttl'] >= new DateTime()) {
                return $this->registry[$key]['value'];
            }
            $this->delete($key);
        }

        return null;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function delete($key)
    {
        if (true === \array_key_exists($key, $this->registry)) {
            unset($this->registry[$key]);
        }
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
        $response = ['ok' => true];

        $this->registry[$key] = [
            'value' => $value,
            'ttl' => new DateTime(\date('Y-m-d H:i:s', \strtotime(\sprintf('now +%s seconds', $ttl))))
        ];

        return \json_encode($response);
    }

    /**
     * @param $base
     *
     * @return mixed
     */
    public function drop($base)
    {
        $this->registry = [];
    }
}
