<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 2:24 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\Sphinx\Connection;

use NilPortugues\Cache\Adapter\SQL\Connection\SphinxPDOConnection;

class SphinxPDOConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $username = '';
    /**
     * @var string
     */
    protected $password = '';
    /**
     * @var string
     */
    protected $dbName = '';
    /**
     * @var string
     */
    protected $host = '127.0.0.1';
    /**
     * @var int
     */
    protected $port = 9306;

    /**
     * @return array
     */
    public function getPDOData()
    {
        return [
            "mysql:host={$this->host};port={$this->port}",
            $this->username,
            $this->password
        ];
    }

    public function testItShouldThrowInvalidArgumentExceptionIfDatabaseDataIsMissing()
    {
        $parameters = [
            SphinxPDOConnection::USER           => '',
            SphinxPDOConnection::PASSWORD       => '',
            SphinxPDOConnection::DRIVER_OPTIONS => [],
        ];
        $this->setExpectedException('InvalidArgumentException');
        $this->getPDOConnection($parameters);
    }

    /**
     * @param   $parameters
     *
     * @return SphinxPDOConnection
     */
    protected function getPDOConnection(array $parameters)
    {
        return new SphinxPDOConnection(
            $parameters,
            $parameters[SphinxPDOConnection::USER],
            $parameters[SphinxPDOConnection::PASSWORD],
            $parameters[SphinxPDOConnection::DRIVER_OPTIONS]
        );
    }

    public function testItShouldThrowInvalidArgumentExceptionOnConnectionFailure()
    {
        $parameters = $this->getConnectionFailParameterArray();
        $this->setExpectedException('InvalidArgumentException');
        $this->getPDOConnection($parameters);
    }

    /**
     * @return array
     */
    public function getConnectionFailParameterArray()
    {
        return [
            SphinxPDOConnection::USER           => $this->username,
            SphinxPDOConnection::PASSWORD       => $this->password,
            SphinxPDOConnection::DATABASE       =>
                [
                    SphinxPDOConnection::HOST    => $this->host,
                    SphinxPDOConnection::PORT    => 99999,
                    SphinxPDOConnection::DB_NAME => $this->dbName,
                ],
            SphinxPDOConnection::DRIVER_OPTIONS => [],
        ];
    }

    public function testItShouldThrowInvalidArgumentExceptionOnConnectionFailureWithSocket()
    {
        $parameters = [
            SphinxPDOConnection::USER           => $this->username,
            SphinxPDOConnection::PASSWORD       => $this->password,
            SphinxPDOConnection::DATABASE       =>
                [
                    SphinxPDOConnection::UNIX_SOCKET => 'path/to/socket/sphinx.sock',
                ],
            SphinxPDOConnection::DRIVER_OPTIONS => [],
        ];
        $this->setExpectedException('InvalidArgumentException');
        $this->getPDOConnection($parameters);
    }
}
