<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 12:17 AM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\Redis;

use NilPortugues\Cache\Adapter\InMemoryAdapter;

/**
 * Class AbstractAdapterTest
 * @package NilPortugues\Tests\Cache\Adapter\Redis
 */
class AbstractAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DummyAdapter
     */
    private $cache;

    protected function setUp()
    {
        $connections = [
            ['host' => '255.0.0.0', 'port'=> 6379, 'database'=> 1, 'alias'=> 'cache1', 'timeout' => 1]
        ];

        $inMemoryAdapter = new InMemoryAdapter();
        $nextAdapter = new InMemoryAdapter();
        $this->cache = new DummyAdapter($connections, $inMemoryAdapter, $nextAdapter);
    }

    protected function tearDown()
    {
        $this->cache->drop();
    }

    public function testItCanGetAndReturnsNull()
    {
        $this->cache->set('cached.value.key', 1, -1);

        $this->assertEquals(null, $this->cache->get('cached.value.key'));
        $this->assertFalse($this->cache->isHit());
    }

    public function testItCanGetAValueFromCacheAndPassItToInMemoryCache()
    {
        $this->assertEquals(1, $this->cache->get('already.cached.value'));
        $this->assertTrue($this->cache->isHit());
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

    public function testItCanDropCache()
    {
        $this->cache->set('cached.value.key', 1, 1);
        $this->cache->drop();

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

    public function testItCanGetAndReturnsValueAndWillExpire()
    {
        $this->cache->set('cached.value.key', 1, 1);
        $this->assertEquals(1, $this->cache->get('cached.value.key'));

        \sleep(2); //Not a bug, Wait for 2 seconds.
        $this->assertEquals(null, $this->cache->get('cached.value.key'));
        $this->assertFalse($this->cache->isHit());
    }
}
