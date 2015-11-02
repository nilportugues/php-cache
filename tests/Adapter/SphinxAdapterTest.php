<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/9/15
 * Time: 8:57 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter;

use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\Adapter\SphinxAdapter;
use NilPortugues\Tests\Cache\Adapter\Sphinx\DummyAdapter;

/**
 * Class SphinxAdapterTest
 * @package NilPortugues\Tests\Cache\Adapter
 */
class SphinxAdapterTest extends \PHPUnit_Framework_TestCase
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
     * @var DummyAdapter
     */
    protected $cache;

    /**
     *
     */
    protected function setUp()
    {
        $this->inMemoryAdapter = new InMemoryAdapter();
        $this->nextAdapter = new InMemoryAdapter();
        $connection = [
            'user'     => '',
            'password' => '',
            'database' => [
                'dbname' => '',
                'host'   => 'localhost',
                'port'   => 3306,
            ],
        ];

        $this->cache = new DummyAdapter($connection, 'cache', $this->inMemoryAdapter, $this->nextAdapter);
    }

    protected function tearDown()
    {
        $this->cache = null;
    }

    /**
     *
     */
    public function testItWillThrowInvalidArgumentException()
    {
        $this->inMemoryAdapter = new InMemoryAdapter();
        $this->nextAdapter = new InMemoryAdapter();
        $connection = [];

        $this->setExpectedException('InvalidArgumentException');
        $this->cache = new SphinxAdapter($connection, '__cache', $this->inMemoryAdapter, $this->nextAdapter);
    }

    public function testItIsAvailable()
    {
        $this->assertTrue($this->cache->isAvailable());
    }

    public function testItCanGetAndReturnsNull()
    {
        $this->assertEquals(null, $this->cache->get('not-cached-key'));
        $this->assertFalse($this->cache->isHit());
    }

    public function testItCanGetAnAlreadyCachedValue()
    {
        $this->assertEquals(10, $this->cache->get('already.cached'));
        $this->assertTrue($this->cache->isHit());
    }

    public function testItCanGetAndReturnsValueWithoutTtl()
    {
        $this->cache->set('cached.value.key', 1);

        $this->assertEquals(1, $this->cache->get('cached.value.key'));
        $this->assertTrue($this->cache->isHit());
    }

    public function testItCanGetReplaceAnExistingValue()
    {
        $this->cache->set('already.cached', 2);

        $this->assertEquals(2, $this->cache->get('already.cached'));
        $this->assertTrue($this->cache->isHit());
    }

    public function testItCanGetAndReturnsValueFromFileSystemAndWillExpire()
    {
        $this->cache->set('cached.value.key', 1, 1);
        $this->inMemoryAdapter->drop();

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

    public function testItWillReturnEmptyForGetIfException()
    {
        $this->forceDummyPDOConnectionToFireExceptions();
        $this->inMemoryAdapter->drop();

        $this->assertEquals(null, $this->cache->get('cached.value.key'));
    }

    public function testItWillCatchIsAvailableException()
    {
        $this->forceDummyPDOConnectionToFireExceptions();
        $this->assertFalse($this->cache->isAvailable());
    }

    /**
     * @return DummyAdapter
     */
    private function forceDummyPDOConnectionToFireExceptions()
    {
        $reflectedCache = new \ReflectionClass($this->cache);
        $property = $reflectedCache->getProperty("connection");
        $property->setAccessible(true);

        $connection = $property->getValue($this->cache);
        $connection->setThrowException();
    }
}
