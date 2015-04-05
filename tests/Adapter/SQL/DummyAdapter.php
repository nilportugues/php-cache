<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 4:55 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\SQL;

use NilPortugues\Cache\Adapter\SQL\AbstractAdapter;

/**
 * Class DummyAdapter
 * @package NilPortugues\Tests\Cache\Adapter\SQL
 */
class DummyAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $connectionClass = DummyPDOConnection::class;
}
