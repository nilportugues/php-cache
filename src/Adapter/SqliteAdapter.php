<?php

namespace NilPortugues\Cache\Adapter;

use NilPortugues\Cache\Adapter\SQL\AbstractAdapter;

/**
 * Class SqliteAdapter
 * @package NilPortugues\Cache\Adapter\Sql
 */
class SqliteAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $connectionClass = '\NilPortugues\Cache\Adapter\SQL\Connection\SqlitePDOConnection';
}
