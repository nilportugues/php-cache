<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/6/15
 * Time: 11:46 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\Memcached;

use NilPortugues\Cache\Adapter\Memcached\MemcachedClient;

/**
 * Class DummyMemcachedClient
 * @package NilPortugues\Tests\Cache\Adapter\Memcached
 */
class DummyMemcachedClient implements MemcachedClient
{
    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        // TODO: Implement get() method.
    }

    /**
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        // TODO: Implement set() method.
    }

    /**
     * @param $key
     * @param $expiration
     */
    public function touch($key, $expiration)
    {
        // TODO: Implement touch() method.
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function delete($key)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @return mixed
     */
    public function getStats()
    {
        return ['some stats'];
    }

    /**
     * @return mixed
     */
    public function flush()
    {
        // TODO: Implement flush() method.
    }
}
