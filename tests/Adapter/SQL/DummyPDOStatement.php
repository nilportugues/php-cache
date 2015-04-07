<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 5:22 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\SQL;

use NilPortugues\Cache\Adapter\SQL\AbstractAdapter;
use PDOException;

/**
 * Class DummyPDOStatement
 * @package NilPortugues\Tests\Cache\Adapter\SQL
 */
class DummyPDOStatement
{
    /**
     * @param DummyPDO $connection
     */
    public function __construct(DummyPDO $connection)
    {
        $this->connection = $connection;

        $this->connection->setCacheId(null);
        $this->connection->setCacheValue(null);
        $this->connection->setCacheTtl(null);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function bindParam($key, $value)
    {
        switch ($key) {
            case AbstractAdapter::QUERY_ID_PLACEHOLDER:
                $this->connection->setCacheId($value);
                break;

            case AbstractAdapter::QUERY_VALUE_PLACEHOLDER:
                $this->connection->setCacheValue($value);
                break;

            case AbstractAdapter::QUERY_TTL_PLACEHOLDER:
                $this->connection->setCacheTtl($value);
                break;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function fetch()
    {
        return $this->connection->get();
    }

    /**
     *
     */
    public function execute()
    {
        if (true === $this->connection->getThrowException()) {
            throw new PDOException();
        }

        switch ($this->connection->getQuery()) {
            case 'DELETE':
                $this->processDeleteCases();
                break;

            case 'UPDATE':
            case 'INSERT':
                $this->connection->set(
                    $this->connection->getCacheId(),
                    $this->connection->getCacheValue(),
                    $this->connection->getCacheTtl()
                );
                break;
        }
    }

    /**
     * @return $this
     */
    private function processDeleteCases()
    {
        $ttl = $this->connection->getCacheTtl();
        
        if (!empty($ttl)) {
            $this->connection->clear();
            return $this;
        }

        $this->connection->drop();
        return $this;
    }
}
