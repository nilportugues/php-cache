<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/9/15
 * Time: 9:00 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\Sphinx;

use NilPortugues\Cache\Adapter\SphinxAdapter;

/**
 * Class DummyAdapter
 * @package NilPortugues\Tests\Cache\Adapter\Sphinx
 */
class DummyAdapter extends SphinxAdapter
{
    /**
     * @var string
     */
    protected $connectionClass = '\NilPortugues\Tests\Cache\Adapter\Sphinx\DummyPDOConnection';
}
