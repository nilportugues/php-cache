<?php

namespace NilPortugues\Cache\Adapter;

use NilPortugues\Cache\Adapter\SQL\AbstractAdapter;

/**
 * Class MySqlAdapter
 * @package NilPortugues\Cache\Adapter\Sql
 */
class MySqlAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $connectionClass = '\NilPortugues\Cache\Adapter\SQL\Connection\MySqlPDOConnection';
}
