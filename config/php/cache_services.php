<?php
include_once realpath(dirname(__FILE__)).'/../../vendor/autoload.php';

use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\Adapter\Redis\NativeAdapter;
use NilPortugues\Cache\Adapter\Redis\PredisAdapter;
use NilPortugues\Cache\Adapter\SQL\MySqlAdapter;
use NilPortugues\Cache\Cache;

$parameters = include_once realpath(dirname(__FILE__)).'/cache_parameters.php';

$inMemoryAdapter = new InMemoryAdapter();

$nativeRedisAdapter = new NativeAdapter(
    $parameters['redis_servers'],
    $inMemoryAdapter
);

$predisRedisAdapter = new PredisAdapter(
    $parameters['redis_servers'],
    $inMemoryAdapter
);

$mysqlAdapter = new MySqlAdapter(
    $parameters['mysql_connection']['master'],
    $parameters['mysql_connection']['cache_table'],
    $inMemoryAdapter
);

return [
    'nil_portugues.cache.adapter.in_memory_adapter' => $inMemoryAdapter,
    'nil_portugues.cache.adapter.sql.mysql_adapter' => $mysqlAdapter,
    'nil_portugues.cache.adapter.redis.native_adapter' => $nativeRedisAdapter,
    'nil_portugues.cache.adapter.redis.predis_adapter' => $predisRedisAdapter,
    'nil_portugues.cache' => new Cache($nativeRedisAdapter, 'namespaced.cache'),
];
