<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 4:56 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\Sphinx;

use NilPortugues\Cache\Adapter\SQL\Connection\AbstractPDOConnection;

/**
 * Class DummyPDOConnection
 * @package NilPortugues\Tests\Cache\Adapter\Sphinx
 */
class DummyPDOConnection extends AbstractPDOConnection
{
    /**
     * @var DummyPDO
     */
    private $pdo;

    /**
     * @param array $parameters
     * @param null  $username
     * @param null  $password
     * @param array $driverOptions
     */
    public function __construct(array $parameters, $username = null, $password = null, array $driverOptions = [])
    {
    }

    /**
     * @return \PDO
     * @codeCoverageIgnore
     */
    public function getConnection()
    {
        $this->pdo = new DummyPDO();

        return $this->pdo;
    }
}
