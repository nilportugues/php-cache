<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/9/15
 * Time: 8:33 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache;

use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\Cache;

/**
 * Class CacheTest
 * @package NilPortugues\Tests\Cache
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cache
     */
    private $cache;

    protected function setUp()
    {
        $this->cache = new Cache(InMemoryAdapter::getInstance(), 'user');
    }

    protected function tearDown()
    {
        $this->cache = null;
    }

    public function testItShouldSetExpires()
    {
        $cache = new Cache(InMemoryAdapter::getInstance(), 'user', 1);
        \sleep(1);
        $this->assertEquals(null, $cache->get('some.key'));
    }

    public function testItSetsValue()
    {
        $this->cache->set('some.key', 1);
        $this->assertEquals(1, $this->cache->get('some.key'));
        $this->assertEquals(true, $this->cache->isHit());
    }

    public function testItDeletesValue()
    {
        $this->cache->set('some.key', 1);
        $this->assertEquals(1, $this->cache->get('some.key'));
        $this->assertEquals(true, $this->cache->isHit());

        $this->cache->delete('some.key');
        $this->assertEquals(null, $this->cache->get('some.key'));
        $this->assertEquals(false, $this->cache->isHit());
    }

    public function testItClears()
    {
        $this->cache->set('some.key', 1, 1);
        $this->assertEquals(1, $this->cache->get('some.key'));

        \sleep(2);
        $this->cache->clear();
        $this->assertEquals(null, $this->cache->get('some.key'));
    }

    public function testItDrops()
    {
        $this->cache->set('some.key1', 1);
        $this->cache->set('some.key2', 2);
        $this->assertEquals(1, $this->cache->get('some.key1'));
        $this->assertEquals(2, $this->cache->get('some.key2'));

        $this->cache->drop();
        $this->assertEquals(null, $this->cache->get('some.key1'));
        $this->assertEquals(null, $this->cache->get('some.key2'));
    }

    public function testItIsAvailable()
    {
        $this->assertTrue($this->cache->isAvailable());
    }
}
