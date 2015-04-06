<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/6/15
 * Time: 10:29 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Cache\Adapter\Memcached;

/**
 * Class MemcachedClient
 * @package NilPortugues\Cache\Adapter\Memcached
 */
interface MemcachedClient
{
    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function set($key, $value);

    /**
     * @param $key
     * @param $expiration
     */
    public function touch($key, $expiration);

    /**
     * @param $key
     *
     * @return mixed
     */
    public function delete($key);

    /**
     * @return mixed
     */
    public function getStats();

    /**
     * @return mixed
     */
    public function flush();
}
