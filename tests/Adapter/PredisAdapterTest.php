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
use NilPortugues\Cache\Adapter\PredisAdapter;

/**
 * Class PredisAdapterTest
 * @package NilPortugues\Tests\Cache\Adapter\Redis
 */
class PredisAdapterTest extends \PHPUnit_Framework_TestCase
{
    private $nextAdapter;

    protected function setUp()
    {
        $this->nextAdapter = InMemoryAdapter::getInstance();
    }

    protected function tearDown()
    {
        $this->nextAdapter = null;
    }

    public function testPredisClientThrowsExceptionAndConnectionIsNotEstablished()
    {
        $connections = [
            ['host' => '255.0.0.0', 'port'=> 6379, 'database'=> 1, 'alias'=> 'cache1']
        ];

        $cache = new PredisAdapter($connections, $this->nextAdapter);
        $this->assertFalse($cache->isAvailable());
    }
}
