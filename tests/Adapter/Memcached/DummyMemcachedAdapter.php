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

use NilPortugues\Cache\Adapter\MemcachedAdapter;

/**
 * Class DummyMemcachedAdapter
 * @package NilPortugues\Tests\Cache\Adapter\Memcached
 */
class DummyMemcachedAdapter extends MemcachedAdapter
{
    /**
     * @param string $persistentId
     * @param array $connections
     *
     * @return DummyMemcachedClient
     */
    protected function getMemcachedClient($persistentId, array $connections)
    {
        return new DummyMemcachedClient($persistentId, $connections);
    }
}
