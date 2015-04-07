<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 4:57 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\SQL;

use PDO;
use PDOException;

/**
 * Class DummyPDO
 * @package NilPortugues\Tests\Cache\Adapter\SQL
 */
class DummyPDO extends PDO
{
    /**
     * @var string
     */
    protected $query = '';

    /**
     * @var bool
     */
    protected $throwException = false;

    public function __construct()
    {
    }

    /**
     * @return $this
     */
    public function setThrowException()
    {
        $this->throwException = true;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getThrowException()
    {
        return $this->throwException;
    }


    /**
     * @param string $query
     * @return int|void
     */
    public function exec($query)
    {
        if (true === $this->throwException) {
            throw new PDOException();
        }
    }


    /**
     * @param $query
     *
     * @return DummyPDOStatement
     */
    public function prepare($query)
    {
        $queryParts = explode(' ', $query);
        $queryParts = array_reverse($queryParts);
        $queryAction = array_pop($queryParts);

        $this->query = $queryAction;

        return new DummyPDOStatement();
    }
}
