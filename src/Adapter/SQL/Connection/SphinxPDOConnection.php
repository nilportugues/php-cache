<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 12:31 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Cache\Adapter\SQL\Connection;

/**
 * Class SphinxPDOConnection
 * @package NilPortugues\Cache\Adapter\SQL\Connection
 */
class SphinxPDOConnection extends AbstractPDOConnection
{
    /**
     * @var array
     */
    protected $keys = [
        self::HOST,
        self::PORT,
    ];
    /**
     * @var string
     */
    protected $dsn = 'mysql:';
    /**
     * @param array $parameters
     *
     * @return string
     */
    protected function buildDSNString(array &$parameters)
    {
        if (array_key_exists(self::UNIX_SOCKET, $parameters)) {
            return self::UNIX_SOCKET."=".$parameters[self::UNIX_SOCKET];
        }
        return parent::buildDSNString($parameters);
    }
}
