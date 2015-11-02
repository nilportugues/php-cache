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

use DateTime;
use NilPortugues\Cache\Adapter\Memcached\MemcachedClient;

/**
 * Class DummyMemcachedClient
 * @package NilPortugues\Tests\Cache\Adapter\Memcached
 */
class DummyMemcachedClient implements MemcachedClient
{
    /**
     * @var array
     */
    private $registry = [];
    
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
     * @param $value
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        $this->registry[$key] = [
            'value' => $value,
            'ttl' => new DateTime(\date('Y-m-d H:i:s', \strtotime(\sprintf('now +10 years'))))
        ];
    }

    /**
     * @param $key
     * @param $expiration
     */
    public function touch($key, $expiration)
    {
        if (true === \array_key_exists($key, $this->registry)) {
            $this->registry[$key]['ttl'] = new DateTime(
                \date('Y-m-d H:i:s', \strtotime(\sprintf('now +s seconds', $expiration)))
            );
        }
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
        $this->registry = [];
    }
}
