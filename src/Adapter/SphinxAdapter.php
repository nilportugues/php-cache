<?php

namespace NilPortugues\Cache\Adapter;

use DateTime;
use NilPortugues\Cache\Adapter\SQL\AbstractAdapter;
use PDO;
use PDOException;

/**
 * Class SphinxAdapter
 * @package NilPortugues\Cache\Adapter
 */
class SphinxAdapter extends AbstractAdapter
{
    const SPHINX_ID_PLACEHOLDER = ':sphinx_id';

    /**
     * @var string
     */
    protected $connectionClass = 'NilPortugues\Cache\Adapter\SQL\Connection\SphinxPDOConnection';

    /**
     * @var array
     */
    protected $requiredKeys = [];


    /**
     * @param     $key
     * @param     $value
     * @param int $ttl
     *
     * @return $this
     */
    protected function insertToDatabase($key, $value, $ttl = 0)
    {
        $value = $this->storageDataStructure($value);

        $calculatedTtl = $this->fromDefaultTtl($ttl);
        $calculatedTtl = new DateTime(\date('Y-m-d H:i:s', $this->getCalculatedTtl($calculatedTtl)));

        $databaseValue = $this->getFromDatabase($key);
        if (false === empty($databaseValue)) {
            $databaseValue[self::CACHE_VALUE] = $value;
            $this->updateToDatabase($key, $databaseValue, $calculatedTtl);
            return $this;
        }

        $stmt = $this->connection->prepare(
            \sprintf(
                'INSERT INTO %s(id,%s,%s,%s) VALUES(%s,%s,%s,%s)',
                $this->cacheTableName,
                self::TABLE_CACHE_ID,
                self::TABLE_CACHE_VALUE,
                self::TABLE_CACHE_TTL,
                self::SPHINX_ID_PLACEHOLDER,
                self::QUERY_ID_PLACEHOLDER,
                self::QUERY_VALUE_PLACEHOLDER,
                self::QUERY_TTL_PLACEHOLDER
            )
        );

        $sphinxId = (int) $this->connection->lastInsertId()+1;

        $stmt->bindParam(self::SPHINX_ID_PLACEHOLDER, $sphinxId, PDO::PARAM_STR);
        $stmt->bindParam(self::QUERY_ID_PLACEHOLDER, $key, PDO::PARAM_STR);
        $stmt->bindParam(self::QUERY_VALUE_PLACEHOLDER, $value, PDO::PARAM_STR);
        $calculatedTtl = $calculatedTtl->format('Y-m-d H:i:s');
        $stmt->bindParam(self::QUERY_TTL_PLACEHOLDER, $calculatedTtl, PDO::PARAM_STR);
        $stmt->execute();

        return $this;
    }


    /**
     * @param $key
     * @param $value
     * @param DateTime $ttl
     * @return $this
     */
    protected function updateToDatabase($key, $value, DateTime $ttl)
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                'REPLACE INTO %s(id, %s, %s, %s) VALUES(%s, %s, %s, %s)',
                $this->cacheTableName,
                self::TABLE_CACHE_ID,
                self::TABLE_CACHE_VALUE,
                self::TABLE_CACHE_TTL,
                self::SPHINX_ID_PLACEHOLDER,
                self::QUERY_ID_PLACEHOLDER,
                self::QUERY_VALUE_PLACEHOLDER,
                self::QUERY_TTL_PLACEHOLDER
            )
        );

        $stmt->bindParam(self::SPHINX_ID_PLACEHOLDER, $value['id'], PDO::PARAM_STR);
        $stmt->bindParam(self::QUERY_ID_PLACEHOLDER, $key, PDO::PARAM_STR);
        $stmt->bindParam(self::QUERY_VALUE_PLACEHOLDER, $value[self::CACHE_VALUE], PDO::PARAM_STR);
        $calculatedTtl = $ttl->format('Y-m-d H:i:s');
        $stmt->bindParam(self::QUERY_TTL_PLACEHOLDER, $calculatedTtl, PDO::PARAM_STR);
        $stmt->execute();

        return $this;
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
                \sprintf(
                    'SELECT id, %s, %s FROM %s WHERE %s = %s',
                    self::TABLE_CACHE_VALUE,
                    self::TABLE_CACHE_TTL,
                    $this->cacheTableName,
                    self::TABLE_CACHE_ID,
                    self::QUERY_ID_PLACEHOLDER
                )
            );

            $stmt->bindParam(self::QUERY_ID_PLACEHOLDER, $key, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (\is_bool($result)) ? [] : $result;
        } catch (PDOException $e) {
            return [];
        }
    }
}
