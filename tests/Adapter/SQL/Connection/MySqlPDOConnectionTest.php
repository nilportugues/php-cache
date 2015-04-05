<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 1:46 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\SQL\Connection;

use NilPortugues\Cache\Adapter\SQL\Connection\MySqlPDOConnection;

class MySqlPDOConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $username = 'root';
    /**
     * @var string
     */
    protected $password = '';
    /**
     * @var string
     */
    protected $port = '3306';
    /**
     * @var string
     */
    protected $host = '127.0.0.1';
    /**
     * @var string
     */
    protected $dbName = '_cache';

    /**
     * @return array
     */
    public function getPDOData()
    {
        return [
            "mysql:host={$this->host};port={$this->port};dbname={$this->dbName}",
            $this->username,
            $this->password
        ];
    }

    /**
     * @return array
     */
    public function getParameterArray()
    {
        return [
            MySqlPDOConnection::USER           => $this->username,
            MySqlPDOConnection::PASSWORD       => $this->password,
            MySqlPDOConnection::DATABASE       =>
                [
                    MySqlPDOConnection::HOST    => $this->host,
                    MySqlPDOConnection::PORT    => $this->port,
                    MySqlPDOConnection::DB_NAME => $this->dbName,
                ],
            MySqlPDOConnection::DRIVER_OPTIONS => [],
        ];
    }

    /**
     * @return array
     */
    public function getConnectionFailParameterArray()
    {
        return [
            MySqlPDOConnection::USER           => $this->username,
            MySqlPDOConnection::PASSWORD       => $this->password,
            MySqlPDOConnection::DATABASE       =>
                [
                    MySqlPDOConnection::HOST    => $this->host,
                    MySqlPDOConnection::PORT    => 99999,
                    MySqlPDOConnection::DB_NAME => $this->dbName,
                ],
            MySqlPDOConnection::DRIVER_OPTIONS => [],
        ];
    }

    /**
     * @param   $parameters
     *
     * @return MySqlPDOConnection
     */
    protected function getPDOConnection(array $parameters)
    {
        return new MySqlPDOConnection(
            $parameters,
            $parameters[MySqlPDOConnection::USER],
            $parameters[MySqlPDOConnection::PASSWORD],
            $parameters[MySqlPDOConnection::DRIVER_OPTIONS]
        );
    }


    public function testItShouldThrowInvalidArgumentExceptionIfDatabaseDataIsMissing()
    {
        $parameters = [
            MySqlPDOConnection::USER           => '',
            MySqlPDOConnection::PASSWORD       => '',
            MySqlPDOConnection::DRIVER_OPTIONS => [],
        ];
        $this->setExpectedException('InvalidArgumentException');
        $this->getPDOConnection($parameters);
    }

    public function testItShouldThrowInvalidArgumentExceptionOnConnectionFailure()
    {
        $parameters = $this->getConnectionFailParameterArray();
        $this->setExpectedException('InvalidArgumentException');
        $this->getPDOConnection($parameters);
    }
}
