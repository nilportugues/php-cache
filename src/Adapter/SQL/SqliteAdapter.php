<?php

namespace NilPortugues\Cache\Adapter\SQL;

use NilPortugues\Cache\Adapter\SQL\Connection\SqlitePDOConnection;

/**
 * Class SqliteAdapter
 * @package NilPortugues\Cache\Adapter\Sql
 */
class SqliteAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $connectionClass =  SqlitePDOConnection::class;
}
