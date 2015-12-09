<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/4/15
 * Time: 9:18 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter;

use NilPortugues\Cache\Adapter\InMemoryAdapter;

/**
 * Class AdapterTest
 * @package NilPortugues\Tests\Cache\Adapter
 */
class AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemoryAdapter
     */
    private $cache;

    protected function setUp()
    {
        $this->cache = InMemoryAdapter::getInstance();
    }

    protected function tearDown()
    {
        $this->cache = null;
    }

    public function testItReturnsHit()
    {
        $this->assertFalse($this->cache->isHit());
    }

    public function testItSetsDefaultTtlAndThrowsException()
    {
        $this->cache->defaultTtl(10);

        $this->setExpectedException('\InvalidArgumentException');
        $this->cache->defaultTtl(-10);
    }

    public function testItReturnsTtlValueFromDefaultTtl()
    {
        $this->cache->defaultTtl(10);
        $this->cache->set('cache.key', 1);

        $this->assertEquals(1, $this->cache->get('cache.key'));
    }
}
