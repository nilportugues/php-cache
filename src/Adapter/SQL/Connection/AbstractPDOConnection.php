<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 12:20 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Cache\Adapter\SQL\Connection;

use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Class AbstractPDOConnection
 * @package NilPortugues\Cache\Adapter\SQL\Connection
 */
abstract class AbstractPDOConnection
{
    const DRIVER         = 'driver';
    const DATABASE       = 'database';
    const SOCKET         = 'socket';
    const USER           = 'user';
    const PASSWORD       = 'password';
    const DRIVER_OPTIONS = 'options';

    /**
     * For MySql
     */
    const HOST        = 'host';
    const PORT        = 'port';
    const DB_NAME     = 'dbname';
    const UNIX_SOCKET = 'unix_socket';
    const CHARSET     = 'charset';

    /**
     * For Sqlite
     */
    const PATH   = 'path';
    const MEMORY = 'memory';

    /**
     * @var PDO
     */
    protected $connection;

    /**
     * @var array
     */
    protected $keys = [];

    /**
     * @var string
     */
    protected $dsn = '';

    /**
     * Attempts to create a connection with the database.
     *
     * @param array       $parameters    All connection parameters passed by the user.
     * @param string|null $username      The username to use when connecting.
     * @param string|null $password      The password to use when connecting.
     * @param array       $driverOptions The driver options to use when connecting.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $parameters, $username = null, $password = null, array $driverOptions = [])
    {
        if (false === array_key_exists(self::DATABASE, $parameters)) {
            throw new InvalidArgumentException(
                sprintf("Parameter array requires of '%s' data to be set.", self::DATABASE)
            );
        }

        try {
            $dsn = $this->buildDSNString($parameters[self::DATABASE]);
            $this->connection = new PDO($this->dsn.$dsn, $username, $password, $driverOptions);
        } catch (PDOException $e) {
            throw new InvalidArgumentException(
                sprintf("An exception occurred in %s: %s", get_class($this), $e->getMessage())
            );
        }
    }

    /**
     * @return PDO
     * @codeCoverageIgnore
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    protected function buildDSNString(array &$parameters)
    {
        $dsn = [];
        foreach ($this->keys as $keyName) {
            if (array_key_exists($keyName, $parameters) && strlen($parameters[$keyName]) > 0) {
                $dsn[] = "{$keyName}={$parameters[$keyName]}";
            }
        }

        return implode(";", $dsn);
    }
}
