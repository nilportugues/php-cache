<?php

namespace NilPortugues\Cache\Adapter\SQL;

use NilPortugues\Cache\Adapter\SQL\Connection\PostgreSqlPDOConnection;

/**
 * Class PostgreSqlAdapter
 * @package NilPortugues\Cache\Adapter\Sql
 */
class PostgreSqlAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $connectionClass = PostgreSqlPDOConnection::class;
}
