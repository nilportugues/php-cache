<?php
include_once realpath(dirname(__FILE__)).'/../../vendor/autoload.php';

use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\Adapter\MemcachedAdapter;
use NilPortugues\Cache\Adapter\RedisAdapter;
use NilPortugues\Cache\Adapter\PredisAdapter;
use NilPortugues\Cache\Adapter\MySqlAdapter;
use NilPortugues\Cache\Cache;

$parameters = include_once realpath(dirname(__FILE__)).'/cache_parameters.php';

$inMemoryAdapter = new InMemoryAdapter();

$nativeRedisAdapter = new RedisAdapter(
    $parameters['redis_servers'],
    $inMemoryAdapter
);

$predisRedisAdapter = new PredisAdapter(
    $parameters['redis_servers'],
    $inMemoryAdapter
);

$mysqlAdapter = new MySqlAdapter(
    $parameters['mysql_servers']['connections'],
    $parameters['mysql_servers']['cache_table'],
    $inMemoryAdapter
);

$sphinxAdapter = new SphinxAdapter(
    $parameters['sphinx_servers']['connections'],
    $parameters['sphinx_servers']['cache_table'],
    $inMemoryAdapter
);


$memcachedAdapter = new MemcachedAdapter(
    $parameters['memcached_servers']['persistent_id'],
    $parameters['memcached_servers']['connections'],
    $inMemoryAdapter
);

return [
    'nil_portugues.cache.adapter.in_memory_adapter' => $inMemoryAdapter,
    'nil_portugues.cache.adapter.memcached_adapter' => $memcachedAdapter,
    'nil_portugues.cache.adapter.sql.mysql_adapter' => $mysqlAdapter,
    'nil_portugues.cache.adapter.sql.sphinx_adapter' => $sphinxAdapter,
    'nil_portugues.cache.adapter.redis.native_adapter' => $nativeRedisAdapter,
    'nil_portugues.cache.adapter.redis.predis_adapter' => $predisRedisAdapter,
    'nil_portugues.user_cache' => new Cache($nativeRedisAdapter, 'user'),
    'nil_portugues.image_cache' => new Cache($nativeRedisAdapter, 'image'),
];
