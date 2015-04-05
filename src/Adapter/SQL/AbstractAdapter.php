<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/5/15
 * Time: 12:16 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Cache\Adapter\SQL;

use DateTime;
use InvalidArgumentException;
use NilPortugues\Cache\Adapter\Adapter;
use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\Adapter\SQL\Connection\AbstractPDOConnection;
use NilPortugues\Cache\CacheAdapter;
use PDO;
use PDOException;

/**
 * Class AbstractAdapter
 * @package NilPortugues\Cache\Adapter\SQL
 */
abstract class AbstractAdapter extends Adapter implements CacheAdapter
{
    const CACHE_TTL   = 'cache_ttl';
    const CACHE_VALUE = 'cache_value';

    const TABLE_CACHE_ID    = 'cache_id';
    const TABLE_CACHE_VALUE = 'cache_value';
    const TABLE_CACHE_TTL   = 'cache_ttl';

    const QUERY_ID_PLACEHOLDER    = ':id';
    const QUERY_VALUE_PLACEHOLDER = ':value';
    const QUERY_TTL_PLACEHOLDER   = ':ttl';

    /**
     * @var string
     */
    protected $connectionClass = '';

    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * @var string
     */
    protected $cacheTableName;

    /**
     * @var InMemoryAdapter
     */
    protected $inMemoryAdapter;

    /**
     * @var CacheAdapter
     */
    protected $nextAdapter;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var array
     */
    protected $requiredKeys = [
        AbstractPDOConnection::USER,
        AbstractPDOConnection::PASSWORD,
        AbstractPDOConnection::DATABASE,
    ];

    /**
     * @param array           $connection
     * @param string          $tableName
     * @param InMemoryAdapter $inMemory
     * @param CacheAdapter    $next
     */
    public function __construct(array $connection, $tableName, InMemoryAdapter $inMemory, CacheAdapter $next = null)
    {
        $this->checkMandatoryParameterFields($connection);
        $this->parameters     = $connection;
        $this->connection     = $this->getConnection();
        $this->cacheTableName = $tableName;

        $this->inMemoryAdapter = $inMemory;
        $this->nextAdapter     = ($inMemory === $next) ? null : $next;
    }

    /**
     * @param array $parameters
     *
     * @throws InvalidArgumentException
     */
    protected function checkMandatoryParameterFields(array &$parameters)
    {
        foreach ($this->requiredKeys as $key) {
            if (false === array_key_exists($key, $parameters)) {
                throw new InvalidArgumentException(
                    sprintf("Parameter '%s' is required to set up a connection.", $key)
                );
            }
        }
    }

    /**
     * @return object
     */
    public function getConnection()
    {
        $class = new \ReflectionClass($this->connectionClass);

        $parameters = [
            $this->parameters,
            (!empty($this->parameters[AbstractPDOConnection::USER]))
                ? $this->parameters[AbstractPDOConnection::USER] : null,
            (!empty($this->parameters[AbstractPDOConnection::PASSWORD]))
                ? $this->parameters[AbstractPDOConnection::PASSWORD] : null,
            (!empty($this->parameters[AbstractPDOConnection::DRIVER_OPTIONS])) ? : [],
        ];

        return $class->newInstanceArgs($parameters);
    }

    /**
     * Get a value identified by $key.
     *
     * @param  string $key
     *
     * @return bool|mixed
     */
    public function get($key)
    {
        $this->hit = false;

        $inMemoryValue = $this->inMemoryAdapter->get($key);
        if ($this->inMemoryAdapter->isHit()) {
            $this->hit = true;
            return $inMemoryValue;
        }

        $result = $this->getFromDatabase($key);

        if (false === empty($result)) {
            $ttl = new DateTime($result[self::CACHE_TTL]);

            if ($ttl >= new DateTime()) {
                $this->hit = true;
                $value     = $this->restoreDataStructure($result[self::CACHE_VALUE]);
                $this->inMemoryAdapter->set($key, $value, 0);
                return $value;
            }
            $this->delete($key);
        }

        return (null !== $this->nextAdapter) ? $this->nextAdapter->get($key) : null;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    protected function getFromDatabase($key)
    {
        try {
            $stmt = $this->connection->prepare(
                sprintf(
                    'SELECT %s, %s FROM %s WHERE %s = %s',
                    self::TABLE_CACHE_VALUE,
                    self::TABLE_CACHE_TTL,
                    $this->cacheTableName,
                    self::TABLE_CACHE_ID,
                    self::QUERY_ID_PLACEHOLDER
                )
            );

            $stmt->bindParam(self::QUERY_ID_PLACEHOLDER, $key, PDO::PARAM_STR);
            $stmt->execute();
            return (array)$stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Delete a value identified by $key.
     *
     * @param string $key
     */
    public function delete($key)
    {
        $this->deleteFromDatabase($key);
        $this->inMemoryAdapter->delete($key);

        if (null !== $this->nextAdapter) {
            $this->nextAdapter->delete($key);
        }
    }

    /**
     * @param string $key
     */
    protected function deleteFromDatabase($key)
    {
        $stmt = $this->connection->prepare(
            sprintf(
                'DELETE FROM %s WHERE %s = %s',
                $this->cacheTableName,
                self::TABLE_CACHE_ID,
                self::QUERY_ID_PLACEHOLDER
            )
        );

        $stmt->bindParam(self::QUERY_ID_PLACEHOLDER, $key, PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Set a value identified by $key and with an optional $ttl.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl
     *
     * @return $this
     */
    public function set($key, $value, $ttl = 0)
    {
        $this->insertToDatabase($key, $value, $ttl);

        $this->inMemoryAdapter->set($key, $value, $ttl);
        if (null !== $this->nextAdapter) {
            $this->nextAdapter->set($key, $value, $ttl);
        }

        return $this;
    }

    /**
     * @param     $key
     * @param     $value
     * @param int $ttl
     *
     * @return $this
     */
    protected function insertToDatabase($key, $value, $ttl = 0)
    {
        $calculatedTtl = $this->fromDefaultTtl($ttl);
        $calculatedTtl = new DateTime(date('Y-m-d H:i:s', $calculatedTtl));

        $databaseValue = $this->getFromDatabase($key);
        if (false === empty($databaseValue)) {
            $this->updateToDatabase($key, $value, $calculatedTtl);
            return $this;
        }

        $stmt = $this->connection->prepare(
            sprintf(
                'INSERT INTO %s(%s, %s, %s) VALUES(%s, %s, %s)',
                $this->cacheTableName,
                self::TABLE_CACHE_ID,
                self::TABLE_CACHE_VALUE,
                self::TABLE_CACHE_TTL,
                self::QUERY_ID_PLACEHOLDER,
                self::QUERY_VALUE_PLACEHOLDER,
                self::QUERY_TTL_PLACEHOLDER
            )
        );

        $stmt->bindParam(self::QUERY_ID_PLACEHOLDER, $key, PDO::PARAM_STR);
        $stmt->bindParam(self::QUERY_VALUE_PLACEHOLDER, $this->storageDataStructure($value), PDO::PARAM_STR);
        $stmt->bindParam(self::QUERY_TTL_PLACEHOLDER, $calculatedTtl->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->execute();

        return $this;
    }

    /**
     * @param     $key
     * @param     $value
     * @param int $ttl
     *
     * @return $this
     */
    private function updateToDatabase($key, $value, $ttl = 0)
    {
        $stmt = $this->connection->prepare(
            sprintf(
                'UPDATE %s SET %s = %s, %s = %s, %s = %s WHERE %s = %s',
                $this->cacheTableName,
                self::TABLE_CACHE_ID,
                self::QUERY_ID_PLACEHOLDER,
                self::TABLE_CACHE_VALUE,
                self::QUERY_VALUE_PLACEHOLDER,
                self::TABLE_CACHE_TTL,
                self::QUERY_TTL_PLACEHOLDER,
                self::TABLE_CACHE_ID,
                self::QUERY_ID_PLACEHOLDER
            )
        );

        $stmt->bindParam(self::QUERY_ID_PLACEHOLDER, $key, PDO::PARAM_STR);
        $stmt->bindParam(self::QUERY_VALUE_PLACEHOLDER, $this->storageDataStructure($value), PDO::PARAM_STR);
        $stmt->bindParam(self::QUERY_TTL_PLACEHOLDER, $ttl, PDO::PARAM_STR);
        $stmt->execute();

        return $this;
    }

    /**
     * Checks the availability of the cache service.
     *
     * @return bool
     */
    public function isAvailable()
    {
        $available = true;

        try {
            $this->connection->exec(
                sprintf(
                    'SELECT %s FROM %s LIMIT 1',
                    self::TABLE_CACHE_ID,
                    $this->cacheTableName
                )
            );
        } catch (PDOException $e) {
            $available = false;
        }

        return $available;
    }

    /**
     * Clears all expired values from cache.
     *
     * @return mixed
     */
    public function clear()
    {
        $this->clearFromDatabase();
        $this->inMemoryAdapter->clear();

        if (null !== $this->nextAdapter) {
            $this->nextAdapter->clear();
        }
    }

    /**
     *
     */
    protected function clearFromDatabase()
    {
        $stmt = $this->connection->prepare(
            sprintf(
                'DELETE FROM %s WHERE %s < %s',
                $this->cacheTableName,
                self::TABLE_CACHE_TTL,
                self::QUERY_TTL_PLACEHOLDER
            )
        );

        $now = new DateTime();
        $stmt->bindParam(self::QUERY_TTL_PLACEHOLDER, $now->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Clears all values from the cache.
     *
     * @return mixed
     */
    public function drop()
    {
        $this->dropFromDatabase();
        $this->inMemoryAdapter->drop();

        if (null !== $this->nextAdapter) {
            $this->nextAdapter->drop();
        }
    }

    /**
     *
     */
    protected function dropFromDatabase()
    {
        $stmt = $this->connection->prepare(
            sprintf('DELETE FROM %s', $this->cacheTableName)
        );

        $stmt->execute();
    }
}
