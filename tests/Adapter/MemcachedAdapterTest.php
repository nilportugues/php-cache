<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/6/15
 * Time: 12:27 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter;

use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Tests\Cache\Adapter\Memcached\DummyMemcachedAdapter;

class MemcachedAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemoryAdapter
     */
    protected $inMemoryAdapter;

    /**
     * @var InMemoryAdapter
     */
    protected $nextAdapter;

    /**
     * @var DummyMemcachedAdapter
     */
    protected $cache;

    protected function setUp()
    {
        $this->inMemoryAdapter = InMemoryAdapter::getInstance();
        $this->nextAdapter = InMemoryAdapter::getInstance();
        $connections = [
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 1
        ];

        $this->cache = new DummyMemcachedAdapter('__cache', [$connections], $this->inMemoryAdapter, $this->nextAdapter);
        $this->cache->drop();
    }

    protected function tearDown()
    {
        $this->cache->drop();
        $this->cache = null;
    }

    public function testItCanGetAndReturnsNull()
    {
        $this->cache->set('cached.value.key', 1, -1);

        $this->assertEquals(null, $this->cache->get('cached.value.key'));
        $this->assertFalse($this->cache->isHit());
    }

    public function testItCanGetAndReturnsValueWithTtl()
    {
        $this->cache->set('cached.value.key', 1, 1000);

        $this->assertEquals(1, $this->cache->get('cached.value.key'));
        $this->assertTrue($this->cache->isHit());
    }

    public function testItCanGetAndReturnsValueWithoutTtl()
    {
        $this->cache->set('cached.value.key', 1);
        $this->inMemoryAdapter->drop();

        $this->assertEquals(1, $this->cache->get('cached.value.key'));
        $this->assertTrue($this->cache->isHit());
    }

    public function testItCanGetDeleteAValue()
    {
        $this->cache->set('cached.value.key', 1);

        $this->assertEquals(1, $this->cache->get('cached.value.key'));
        $this->assertTrue($this->cache->isHit());

        $this->cache->delete('cached.value.key');
        $this->assertEquals(null, $this->cache->get('cached.value.key'));
        $this->assertFalse($this->cache->isHit());
    }

    public function testItCanGetAndReturnsValueAndWillExpire()
    {
        $this->cache->set('cached.value.key', 1, 1);

        \sleep(2); //Not a bug, Wait for 2 seconds.
        $this->assertEquals(null, $this->cache->get('cached.value.key'));
        $this->assertFalse($this->cache->isHit());
    }

    public function testItCanClearValues()
    {
        $this->cache->set('cached.value.key1', 1, 1);
        $this->cache->set('cached.value.key2', 2, 1);

        $this->assertEquals(1, $this->cache->get('cached.value.key1'));
        $this->assertEquals(2, $this->cache->get('cached.value.key2'));

        \sleep(2); //Not a bug, Wait for 2 seconds.
        $this->cache->clear();
        $this->assertEquals(null, $this->cache->get('cached.value.key1'));
        $this->assertEquals(null, $this->cache->get('cached.value.key2'));
    }

    public function testItCanDropCache()
    {
        $this->cache->set('cached.value.key', 1, 1);
        $this->cache->drop();

        $this->assertEquals(null, $this->cache->get('cached.value.key'));
        $this->assertFalse($this->cache->isHit());
    }

    public function testItIsAvailable()
    {
        $this->assertTrue($this->cache->isAvailable());
    }
}
