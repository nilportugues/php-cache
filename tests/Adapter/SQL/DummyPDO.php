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

use DateTime;
use NilPortugues\Cache\Adapter\SQL\AbstractAdapter;
use PDO;
use PDOException;

/**
 * Class DummyPDO
 * @package NilPortugues\Tests\Cache\Adapter\SQL
 */
class DummyPDO extends PDO
{
    /**
     * @var array
     */
    protected $registry = [];

    /**
     * @var string
     */
    protected $key = '';

    /**
     * @var string
     */
    protected $query = '';

    /**
     * @var string
     */
    private $cacheId;

    /**
     * @var string
     */
    private $cacheTtl;

    /**
     * @var string
     */
    private $cacheValue;

    /**
     * @var bool
     */
    protected $throwException = false;

    public function __construct()
    {
        $this->registry['already.cached'] = [
            AbstractAdapter::TABLE_CACHE_VALUE => serialize(10),
            AbstractAdapter::TABLE_CACHE_TTL => '2025-01-01 10:10:10'
        ];
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
     *
     * @return int|void
     * @throws \PDOException
     */
    public function exec($query)
    {
        if (true === $this->throwException) {
            throw new PDOException();
        }
    }


    /**
     * @param string $statement
     * @param null   $options
     *
     * @return DummyPDOStatement|\PDOStatement
     */
    public function prepare($statement, $options = null)
    {
        $queryParts = explode(' ', $statement);
        $queryParts = array_reverse($queryParts);
        $queryAction = array_pop($queryParts);

        $this->query = $queryAction;

        return new DummyPDOStatement($this);
    }

    /**
     * @return mixed
     */
    public function getCacheId()
    {
        return $this->cacheId;
    }

    /**
     * @param mixed $cacheId
     *
     * @return $this
     */
    public function setCacheId($cacheId)
    {
        $this->cacheId = $cacheId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCacheTtl()
    {
        return $this->cacheTtl;
    }

    /**
     * @param mixed $cacheTtl
     *
     * @return $this
     */
    public function setCacheTtl($cacheTtl)
    {
        $this->cacheTtl = $cacheTtl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCacheValue()
    {
        return $this->cacheValue;
    }

    /**
     * @param mixed $cacheValue
     *
     * @return $this
     */
    public function setCacheValue($cacheValue)
    {
        $this->cacheValue = $cacheValue;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }


    /**
     * @return array
     */
    public function get()
    {
        $value = [];

        if (true === array_key_exists($this->getCacheId(), $this->registry)) {
            $value = $this->registry[$this->getCacheId()];
        }

        $this->cacheId = null;
        $this->cacheTtl = null;
        $this->cacheValue = null;

        return $value;
    }

    /**
     * @param $key
     * @param $value
     * @param $ttl
     */
    public function set($key, $value, $ttl)
    {
        $this->registry[$key] = [
            AbstractAdapter::TABLE_CACHE_VALUE => $value,
            AbstractAdapter::TABLE_CACHE_TTL => $ttl
        ];
    }

    /**
     *
     */
    public function clear()
    {
        $now = new DateTime();
        foreach (array_keys($this->registry) as $key) {
            $ttl = new DateTime($this->registry[$key][AbstractAdapter::TABLE_CACHE_TTL]);

            if ($ttl < $now) {
                unset($this->registry[$key]);
            }
        }
    }

    /**
     *
     */
    public function drop()
    {
        $this->registry = [];
    }
}
