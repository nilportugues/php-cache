<?php

namespace NilPortugues\Cache\Adapter;

use NilPortugues\Cache\Adapter\SQL\AbstractAdapter;

/**
 * Class PostgreSqlAdapter
 * @package NilPortugues\Cache\Adapter\Sql
 */
class PostgreSqlAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $connectionClass = '\NilPortugues\Cache\Adapter\SQL\Connection\PostgreSqlPDOConnection';
}
