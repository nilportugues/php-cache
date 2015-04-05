<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 4:57 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\SQL;

use PDO;

/**
 * Class DummyPDO
 * @package NilPortugues\Tests\Cache\Adapter\SQL
 */
class DummyPDO extends PDO
{
    public function __construct()
    {
    }
}
