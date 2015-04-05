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
        $this->parameters = $connection;
        $this->connection = $this->getConnection();
        $this->cacheTableName = $tableName;

        $this->inMemoryAdapter = $inMemory;
        $this->nextAdapter     = ($inMemory === $next) ? null : $next;
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
            (!empty($this->parameters[AbstractPDOConnection::DRIVER_OPTIONS])) ?: [],
        ];

        return $class->newInstanceArgs($parameters);
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
            $ttl = new DateTime($result['cache_ttl']);

            if ($ttl >= new DateTime()) {
                $this->hit = true;
                $value     = $this->restoreDataStructure($result['cache_value']);
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
                sprintf('SELECT cache_value FROM %s WHERE cache_id = :id', $this->cacheTableName)
            );

            $stmt->bindParam(':id', $key, PDO::PARAM_STR);
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
            sprintf('DELETE FROM %s WHERE cache_id = :id', $this->cacheTableName)
        );

        $stmt->bindParam(':id', $key, PDO::PARAM_STR);
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
     */
    protected function insertToDatabase($key, $value, $ttl = 0)
    {
        $stmt = $this->connection->prepare(
            sprintf(
                'INSERT INTO %s(cache_id, cache_value, cache_ttl) VALUES(:id, :value, :ttl)',
                $this->cacheTableName
            )
        );

        $calculatedTtl = $this->fromDefaultTtl($ttl);
        $calculatedTtl = new DateTime(date('Y-m-d H:i:s', $calculatedTtl));

        $stmt->bindParam(':id', $key, PDO::PARAM_STR);
        $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        $stmt->bindParam(':ttl', $calculatedTtl->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->execute();
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
                sprintf('SELECT cache_id FROM %s LIMIT 1', $this->cacheTableName)
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
            sprintf('DELETE FROM %s WHERE cache_ttl < :ttl', $this->cacheTableName)
        );

        $now = new DateTime();
        $stmt->bindParam(':ttl', $now->format('Y-m-d H:i:s'), PDO::PARAM_STR);
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
