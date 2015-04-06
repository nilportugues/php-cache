<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/6/15
 * Time: 9:00 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Cache\Adapter\ElasticSearch;

/**
 * Interface CurlClient
 * @package NilPortugues\Cache\Adapter\ElasticSearch
 */
interface CurlClient
{
    /**
     * @return mixed
     */
    public function cacheIndexExists();

    /**
     * @param       $base
     * @param array $createCache
     *
     * @return mixed
     */
    public function createCacheIndex($base, array $createCache);

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     *
     * @return mixed
     */
    public function delete($key);

    /**
     * @param $key
     * @param $value
     * @param $ttl
     *
     * @return mixed
     */
    public function set($key, $value, $ttl);

    /**
     * @param $base
     *
     * @return mixed
     */
    public function drop($base);
}
