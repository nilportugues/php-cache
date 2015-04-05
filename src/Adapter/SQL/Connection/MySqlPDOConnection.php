<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 12:29 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Cache\Adapter\SQL\Connection;

/**
 * Class MySqlPDOConnection
 * @package NilPortugues\Cache\Adapter\SQL\Connection
 */
class MySqlPDOConnection extends AbstractPDOConnection
{
    /**
     * @var array
     */
    protected $keys = [
        self::HOST,
        self::PORT,
        self::DB_NAME,
        self::UNIX_SOCKET,
        self::CHARSET,
    ];

    /**
     * @var string
     */
    protected $dsn = 'mysql:';
}
