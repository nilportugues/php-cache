<?php

namespace NilPortugues\Tests\Cache\Adapter;

use DateTime;
use NilPortugues\Cache\Adapter\InMemoryAdapter;

/**
 * Class InMemoryAdapterTest
 * @package NilPortugues\Tests\Cache\Adapter
 */
class InMemoryAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemoryAdapter
     */
    protected $cache;

    /**
     * @return InMemoryAdapter
     */
    protected function setUp()
    {
        $this->cache = new InMemoryAdapter();
    }
    
    protected function tearDown()
    {
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

        sleep(2); //Not a bug, Wait for 2 seconds.
        $this->assertEquals(null, $this->cache->get('cached.value.key'));
        $this->assertFalse($this->cache->isHit());
    }

    public function testItCanClearValues()
    {
        $this->cache->set('cached.value.key1', 1, 1);
        $this->cache->set('cached.value.key2', 2, 1);

        $this->assertEquals(1, $this->cache->get('cached.value.key1'));
        $this->assertEquals(2, $this->cache->get('cached.value.key2'));

        sleep(2); //Not a bug, Wait for 2 seconds.
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


    public function testItCanCacheAnObject()
    {
        $data = new DateTime('now');

        $this->cache->set('cached.value.key', $data, 1);

        $this->assertEquals($data, $this->cache->get('cached.value.key'));
        $this->assertFalse($data === $this->cache->get('cached.value.key'));
    }

    public function testItCanCacheAnArray()
    {
        $data = [new DateTime('now')];

        $this->cache->set('cached.value.key', $data, 1);
        $this->assertEquals($data, $this->cache->get('cached.value.key'));
    }
}
