<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/3/15
 * Time: 7:06 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\Redis;

use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\Adapter\RedisAdapter;

/**
 * Class NativeAdapterTest
 * @package NilPortugues\Tests\Cache\Adapter\Redis
 */
class NativeAdapterTest extends \PHPUnit_Framework_TestCase
{
    private $nextAdapter;
    private $inMemoryAdapter;

    protected function setUp()
    {
        $this->inMemoryAdapter = InMemoryAdapter::getInstance();
        $this->nextAdapter = InMemoryAdapter::getInstance();
    }

    protected function tearDown()
    {
        $this->inMemoryAdapter = null;
        $this->nextAdapter = null;
    }

    public function testNativeClientThrowsExceptionAndConnectionIsNotEstablished()
    {
        $connections = [
            ['host' => '255.0.0.0', 'port'=> 6379, 'database'=> 1, 'alias'=> 'cache1', 'timeout' => 1]
        ];

        $cache = new RedisAdapter($connections, $this->inMemoryAdapter, $this->nextAdapter);
        $this->assertFalse($cache->isAvailable());
    }
}
