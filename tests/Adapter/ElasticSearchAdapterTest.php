<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/6/15
 * Time: 8:54 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter;

use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Tests\Cache\Adapter\ElasticSearch\DummyElasticSearchAdapter;

/**
 * Class ElasticSearchAdapterTest
 * @package NilPortugues\Tests\Cache\Adapter
 */
class ElasticSearchAdapterTest extends \PHPUnit_Framework_TestCase
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
     * @var DummyElasticSearchAdapter
     */
    private $cache;

    /**
     *
     */
    protected function setUp()
    {
        $this->inMemoryAdapter = InMemoryAdapter::getInstance();
        $this->nextAdapter = InMemoryAdapter::getInstance();
        $baseUrl = 'http://localhost:9200';

        $this->cache = new DummyElasticSearchAdapter($baseUrl, 'cache', $this->inMemoryAdapter, $this->nextAdapter);
    }

    /**
     *
     */
    protected function tearDown()
    {
        $this->cache = null;
    }

    public function testItThrowsExceptionIfProvidedBaseUrlIsNotAValidUrl()
    {
        $this->setExpectedException('InvalidArgumentException');

        $baseUrl = 'AAAA';
        new DummyElasticSearchAdapter($baseUrl, 'cache', $this->inMemoryAdapter, $this->nextAdapter);
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

    public function testItCanGetAnAlreadyCachedValue()
    {
        $this->assertEquals(1, $this->cache->get('already.cached.value'));
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
        $this->assertInternalType('bool', $this->cache->isAvailable());
    }
}
