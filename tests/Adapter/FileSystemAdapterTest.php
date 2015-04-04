<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/3/15
 * Time: 7:07 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter;

use NilPortugues\Cache\Adapter\FileSystemAdapter;
use NilPortugues\Cache\Adapter\InMemoryAdapter;

/**
 * Class FileSystemAdapterTest
 * @package NilPortugues\Tests\Cache\Adapter
 */
class FileSystemAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileSystemAdapter
     */
    private $cache;

    /**
     * @var InMemoryAdapter
     */
    private $inMemoryAdapter;

    public function setUp()
    {
        $cacheDir = realpath(dirname(__FILE__)).'/tmp';
        if (file_exists($cacheDir)) {
            $this->removeDirectory($cacheDir);
        }
        mkdir($cacheDir);

        $this->inMemoryAdapter = new InMemoryAdapter();
        $nextAdapter = new InMemoryAdapter();

        $this->cache = new FileSystemAdapter($cacheDir, $this->inMemoryAdapter, $nextAdapter);
    }

    public function tearDown()
    {
        $directory = realpath(dirname(__FILE__)).'/tmp';

        if (true === file_exists($directory)) {
            $this->removeDirectory($directory);
        }
        $this->cache = null;
    }

    public function testItThrowsExceptionWhenCacheDirDoesNotExist()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->inMemoryAdapter = new InMemoryAdapter();
        $nextAdapter = new InMemoryAdapter();

        new FileSystemAdapter('./a', $this->inMemoryAdapter, $nextAdapter);
    }


    public function testItThrowsExceptionWhenCacheDirIsNotADirectory()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->inMemoryAdapter = new InMemoryAdapter();
        $nextAdapter = new InMemoryAdapter();

        new FileSystemAdapter(__FILE__, $this->inMemoryAdapter, $nextAdapter);
    }


    public function testItThrowsExceptionWhenCacheDirIsNotWritable()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->inMemoryAdapter = new InMemoryAdapter();
        $nextAdapter = new InMemoryAdapter();

        new FileSystemAdapter('/', $this->inMemoryAdapter, $nextAdapter);
    }

    public function testItCanGetAndReturnsNull()
    {
        $this->cache->set('cached.value.key', 1, -1);

        $this->assertEquals(null, $this->cache->get('cached.value.key'));
        $this->assertFalse($this->cache->isHit());
    }

    public function testItCanGetAndReturnsValueWithoutTtl()
    {
        $this->cache->set('cached.value.key', 1);

        $this->assertEquals(1, $this->cache->get('cached.value.key'));
        $this->assertTrue($this->cache->isHit());
    }

    public function testItCanGetAndReturnsValueFromMemoryWithTtl()
    {
        $this->cache->set('cached.value.key', 1, 1000);

        $this->assertEquals(1, $this->cache->get('cached.value.key'));
        $this->assertTrue($this->cache->isHit());
    }

    public function testItCanGetAndReturnsValueFromFileSystemWithTtl()
    {
        $this->cache->set('cached.value.key', 1, 1000);
        $this->inMemoryAdapter->drop();

        $this->assertEquals(1, $this->cache->get('cached.value.key'));
        $this->assertTrue($this->cache->isHit());
    }

    public function testItCanGetAndReturnsValueFromFileSystemAndWillExpire()
    {
        $this->cache->set('cached.value.key', 1, 1);
        $this->inMemoryAdapter->drop();

        sleep(2); //Not a bug, Wait for 2 seconds.
        $this->assertEquals(null, $this->cache->get('cached.value.key'));
        $this->assertFalse($this->cache->isHit());
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

    private function removeDirectory($directory)
    {
        foreach (glob("{$directory}/*") as $file) {
            if (is_dir($file)) {
                $this->removeDirectory($file);
            } else {
                unlink($file);
            }
        }
        rmdir($directory);
    }
}
