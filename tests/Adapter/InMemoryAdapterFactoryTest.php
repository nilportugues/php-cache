<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/9/15
 * Time: 4:42 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter;

use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\Adapter\InMemoryAdapterFactory;

class InMemoryAdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testItCreatesInMemoryAdapter()
    {
        $this->assertInstanceOf(
            InMemoryAdapter::class,
            InMemoryAdapterFactory::create()
        );
    }
}
