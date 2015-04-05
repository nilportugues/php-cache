<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 2:25 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\SQL\Connection;

use NilPortugues\Cache\Adapter\SQL\Connection\SqlitePDOConnection;

class SqlitePDOConnectionTest extends \PHPUnit_Framework_TestCase
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
     * @return array
     */
    public function getConnectionFailParameterArray()
    {
        return [
            SqlitePDOConnection::USER           => $this->username,
            SqlitePDOConnection::PASSWORD       => $this->password,
            SqlitePDOConnection::DATABASE       =>
                [
                    SqlitePDOConnection::PATH => '/dev/null/a.db',
                ],
            SqlitePDOConnection::DRIVER_OPTIONS => [],
        ];
    }

    /**
     * @param   $parameters
     *
     * @return SqlitePDOConnection
     */
    protected function getPDOConnection(array $parameters)
    {
        return new SqlitePDOConnection(
            $parameters,
            $parameters[SqlitePDOConnection::USER],
            $parameters[SqlitePDOConnection::PASSWORD],
            $parameters[SqlitePDOConnection::DRIVER_OPTIONS]
        );
    }

    public function testItShouldThrowInvalidArgumentExceptionIfDatabaseDataIsMissing()
    {
        $parameters = [
            SqlitePDOConnection::USER           => '',
            SqlitePDOConnection::PASSWORD       => '',
            SqlitePDOConnection::DRIVER_OPTIONS => [],
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
