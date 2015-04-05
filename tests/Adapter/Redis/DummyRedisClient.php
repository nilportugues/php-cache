<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 12:10 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\Redis;

use DateTime;

/**
 * Class DummyRedisClient
 * @package NilPortugues\Tests\Cache\Adapter\Redis
 */
class DummyRedisClient
{
    /**
     * @var array
     */
    private $redis = [];

    /**
     *
     */
    public function __construct()
    {
        $this->redis = [
            'already.cached.value' => [
                'value' => serialize(1),
                'ttl' => new DateTime('2215-04-04 11:38:25'),
            ],
        ];
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->redis)) {
            if ($this->redis[$key]['ttl'] >= (new DateTime())) {
                return $this->redis[$key]['value'];
            }
            unset($this->redis[$key]);
        }

        return false;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $ttl = strtotime(sprintf('now +%s seconds', 0));

        $this->redis[$key] = [
            'value' => $value,
            'ttl' => new DateTime(date('Y-m-d H:i:s', $ttl))
        ];
    }

    /**
     * @param $key
     * @param $ttl
     */
    public function expire($key, $ttl)
    {
        $ttl = strtotime(sprintf('now +%s seconds', $ttl));
        $this->redis[$key]['ttl'] = new DateTime(date('Y-m-d H:i:s', $ttl));
    }

    /**
     * @param $key
     */
    public function del($key)
    {
        if (array_key_exists($key, $this->redis)) {
            unset($this->redis[$key]);
        }
    }

    /**
     *
     */
    public function flushDB()
    {
        $this->redis = [];
    }
}
