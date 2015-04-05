<?php

namespace NilPortugues\Cache\Adapter;

use NilPortugues\Cache\Adapter\SQL\AbstractAdapter;
use NilPortugues\Cache\Adapter\SQL\Connection\SphinxPDOConnection;

/**
 * Class SphinxAdapter
 * @package NilPortugues\Cache\Adapter
 */
class SphinxAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $connectionClass = SphinxPDOConnection::class;

    /**
     * @var array
     */
    protected $requiredKeys = [];
}
