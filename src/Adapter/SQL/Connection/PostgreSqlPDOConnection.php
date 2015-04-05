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
 * Class PostgreSqlPDOConnection
 * @package NilPortugues\Cache\Adapter\SQL\Connection
 */
class PostgreSqlPDOConnection extends AbstractPDOConnection
{
    /**
     * @var array
     */
    protected $keys = [
        self::HOST,
        self::PORT,
        self::DB_NAME,
    ];
    /**
     * @var string
     */
    protected $dsn = 'pgsql:';
}
