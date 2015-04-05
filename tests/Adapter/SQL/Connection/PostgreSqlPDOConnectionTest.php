<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 2:24 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\SQL\Connection;

use NilPortugues\Cache\Adapter\SQL\Connection\PostgreSqlPDOConnection;

class PostgreSqlPDOConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $username = 'postgres';
    /**
     * @var string
     */
    protected $password = '';
    /**
     * @var string
     */
    protected $dbName = 'testDDD';
    /**
     * @var string
     */
    protected $port = '5432';
    /**
     * @var string
     */
    protected $host = '127.0.0.1';
    /**
     * @return array
     */
    public function getPDOData()
    {
        return [
            "pgsql:host={$this->host};port={$this->port};dbname={$this->dbName}",
            $this->username,
            $this->password
        ];
    }
    
    /**
     * @return array
     */
    public function getConnectionFailParameterArray()
    {
        return [
            PostgreSqlPDOConnection::USER           => $this->username,
            PostgreSqlPDOConnection::PASSWORD       => $this->password,
            PostgreSqlPDOConnection::DATABASE       =>
                [
                    PostgreSqlPDOConnection::HOST    => $this->host,
                    PostgreSqlPDOConnection::PORT    => 99999,
                    PostgreSqlPDOConnection::DB_NAME => $this->dbName,
                ],
            PostgreSqlPDOConnection::DRIVER_OPTIONS => [],
        ];
    }
    /**
     * @param   $parameters
     *
     * @return PostgreSqlPDOConnection
     */
    protected function getPDOConnection(array $parameters)
    {
        return new PostgreSqlPDOConnection(
            $parameters,
            $parameters[PostgreSqlPDOConnection::USER],
            $parameters[PostgreSqlPDOConnection::PASSWORD],
            $parameters[PostgreSqlPDOConnection::DRIVER_OPTIONS]
        );
    }

    public function testItShouldThrowInvalidArgumentExceptionIfDatabaseDataIsMissing()
    {
        $parameters = [
            PostgreSqlPDOConnection::USER           => '',
            PostgreSqlPDOConnection::PASSWORD       => '',
            PostgreSqlPDOConnection::DRIVER_OPTIONS => [],
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
