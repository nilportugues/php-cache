<?php

namespace NilPortugues\Tests\Cache\Adapter;

use NilPortugues\Cache\Adapter\InMemoryAdapter;

/**
 * Class InMemoryAdapterTest
 * @package NilPortugues\Tests\Cache\Adapter
 */
class InMemoryAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return InMemoryAdapter
     */
    private function getCacheDriver()
    {
        return new InMemoryAdapter();
    }

    public function testItCanGetAndReturnsNull()
    {
        $cache = $this->getCacheDriver();
        $cache->set('cached.value.key', 1, -1);

        $this->assertEquals(null, $cache->get('cached.value.key'));
        $this->assertFalse($cache->isHit());
    }

    public function testItCanGetAndReturnsValueWithTtl()
    {
        $cache = $this->getCacheDriver();
        $cache->set('cached.value.key', 1, 1000);

        $this->assertEquals(1, $cache->get('cached.value.key'));
        $this->assertTrue($cache->isHit());
    }

    public function testItCanGetAndReturnsValueWithoutTtl()
    {
        $cache = $this->getCacheDriver();
        $cache->set('cached.value.key', 1);

        $this->assertEquals(1, $cache->get('cached.value.key'));
        $this->assertTrue($cache->isHit());
    }

    public function testItCanGetDeleteAValue()
    {
        $cache = $this->getCacheDriver();
        $cache->set('cached.value.key', 1);

        $this->assertEquals(1, $cache->get('cached.value.key'));
        $this->assertTrue($cache->isHit());

        $cache->delete('cached.value.key');
        $this->assertEquals(null, $cache->get('cached.value.key'));
        $this->assertFalse($cache->isHit());
    }

    public function testItCanGetAndReturnsValueAndWillExpire()
    {
        $cache = $this->getCacheDriver();
        $cache->set('cached.value.key', 1, 1);

        sleep(2); //Not a bug, Wait for 2 seconds.
        $this->assertEquals(null, $cache->get('cached.value.key'));
        $this->assertFalse($cache->isHit());
    }

    public function testItCanClearValues()
    {
        $cache = $this->getCacheDriver();
        $cache->set('cached.value.key1', 1, 1);
        $cache->set('cached.value.key2', 2, 1);

        $this->assertEquals(1, $cache->get('cached.value.key1'));
        $this->assertEquals(2, $cache->get('cached.value.key2'));

        sleep(2); //Not a bug, Wait for 2 seconds.
        $cache->clear();
        $this->assertEquals(null, $cache->get('cached.value.key1'));
        $this->assertEquals(null, $cache->get('cached.value.key2'));
    }


    public function testItCanDropCache()
    {
        $cache = $this->getCacheDriver();
        $cache->set('cached.value.key', 1, 1);
        $cache->drop();

        $this->assertEquals(null, $cache->get('cached.value.key'));
        $this->assertFalse($cache->isHit());
    }

    public function testItIsAvailable()
    {
        $cache = $this->getCacheDriver();
        $this->assertTrue($cache->isAvailable());
    }
}
