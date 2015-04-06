<?php

namespace NilPortugues\Cache\Adapter;

use NilPortugues\Cache\Adapter\SQL\AbstractAdapter;

/**
 * Class SphinxAdapter
 * @package NilPortugues\Cache\Adapter
 */
class SphinxAdapter extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $connectionClass = 'NilPortugues\Cache\Adapter\SQL\Connection\SphinxPDOConnection';

    /**
     * @var array
     */
    protected $requiredKeys = [];
}
