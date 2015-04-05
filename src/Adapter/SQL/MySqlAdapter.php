<?php

namespace NilPortugues\Cache\Adapter\SQL;

use NilPortugues\Cache\Adapter\SQL\Connection\MySqlPDOConnection;

/**
 * Class MySqlAdapter
 * @package NilPortugues\Cache\Adapter\Sql
 */
class MySqlAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $connectionClass = MySqlPDOConnection::class;
}
