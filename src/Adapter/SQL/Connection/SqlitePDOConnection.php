<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 12:32 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Cache\Adapter\SQL\Connection;

/**
 * Class SqlitePDOConnection
 * @package NilPortugues\Cache\Adapter\SQL\Connection
 */
class SqlitePDOConnection extends AbstractPDOConnection
{
    /**
     * @var array
     */
    protected $keys = [
        self::PATH,
        self::MEMORY,
    ];

    /**
     * @var string
     */
    protected $dsn = 'sqlite:';

    /**
     * @param array $parameters
     *
     * @return string
     */
    protected function buildDSNString(array &$parameters)
    {
        return str_replace(self::PATH."=", '', parent::buildDSNString($parameters));
    }
}
