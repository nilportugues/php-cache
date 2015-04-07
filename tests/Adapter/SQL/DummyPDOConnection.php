<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 4:56 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\SQL;

use DateTime;
use NilPortugues\Cache\Adapter\SQL\AbstractAdapter;
use NilPortugues\Cache\Adapter\SQL\Connection\AbstractPDOConnection;

/**
 * Class DummyPDOConnection
 * @package NilPortugues\Tests\Cache\Adapter\SQL
 */
class DummyPDOConnection extends AbstractPDOConnection
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
     * @param array $parameters
     * @param null  $username
     * @param null  $password
     * @param array $driverOptions
     */
    public function __construct(array $parameters, $username = null, $password = null, array $driverOptions = [])
    {
        $this->registry['already.cached'] = [
            AbstractAdapter::TABLE_CACHE_VALUE => serialize(10),
            AbstractAdapter::TABLE_CACHE_TTL => '2025-01-01 10:10:10'
        ];
    }

    /**
     * @return \PDO
     * @codeCoverageIgnore
     */
    public function getConnection()
    {
        return new DummyPDO();
    }




    /**
     * @return array
     */
    public function get()
    {
        $value = [];

        if (true === array_key_exists($this->cacheId, $this->registry)) {
            $value = $this->registry[$this->cacheId];
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

    public function drop()
    {
        $this->registry = [];
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
}
