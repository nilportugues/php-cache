<?php
include_once realpath(dirname(__FILE__)).'/../../vendor/autoload.php';

use NilPortugues\Cache\Adapter\ElasticSearchAdapter;
use NilPortugues\Cache\Adapter\FileSystemAdapter;
use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\Adapter\MemcachedAdapter;
use NilPortugues\Cache\Adapter\PostgreSqlAdapter;
use NilPortugues\Cache\Adapter\RedisAdapter;
use NilPortugues\Cache\Adapter\PredisAdapter;
use NilPortugues\Cache\Adapter\MySqlAdapter;
use NilPortugues\Cache\Adapter\SphinxAdapter;
use NilPortugues\Cache\Adapter\SqliteAdapter;
use NilPortugues\Cache\Cache;

$parameters = include_once realpath(dirname(__FILE__)).'/cache_parameters.php';

/******************************************************
 * Caching in system memory or disk
 ******************************************************/
$inMemoryAdapter = new InMemoryAdapter();

$fileSystemAdapter = new FileSystemAdapter(
    $parameters['filesystem']['path'],
    $inMemoryAdapter
);

/*******************************************************
 * Caching using cache systems
 ******************************************************/
$nativeRedisAdapter = new RedisAdapter(
    $parameters['redis'],
    $inMemoryAdapter
);

$predisRedisAdapter = new PredisAdapter(
    $parameters['redis'],
    $inMemoryAdapter
);

$memcachedAdapter = new MemcachedAdapter(
    $parameters['memcached']['persistent_id'],
    $parameters['memcached']['connections'],
    $inMemoryAdapter
);

/*******************************************************
 * Caching using the database
 ******************************************************/
$mysqlAdapter = new MySqlAdapter(
    $parameters['mysql']['connections'],
    $parameters['mysql']['cache_table'],
    $inMemoryAdapter
);

$postgresqlAdapter = new PostgreSqlAdapter(
    $parameters['postgresql']['connections'],
    $parameters['postgresql']['cache_table'],
    $inMemoryAdapter
);

$sqliteAdapter = new SqliteAdapter(
    $parameters['sqlite']['connections'],
    $parameters['sqlite']['cache_table'],
    $inMemoryAdapter
);

/*******************************************************
 * Caching using full-text engines
 ******************************************************/
$sphinxAdapter = new SphinxAdapter(
    $parameters['sphinx']['connections'],
    $parameters['sphinx']['cache_table'],
    $inMemoryAdapter
);

$elasticSearchAdapter = new ElasticSearchAdapter(
    $parameters['elastic']['base_url'],
    $parameters['elastic']['index_name'],
    $inMemoryAdapter
);

return [
    'nil_portugues.cache.adapter.in_memory_adapter' => $inMemoryAdapter,
    'nil_portugues.cache.adapter.file_system_adapter' => $fileSystemAdapter,
    'nil_portugues.cache.adapter.redis.native_adapter' => $nativeRedisAdapter,
    'nil_portugues.cache.adapter.redis.predis_adapter' => $predisRedisAdapter,
    'nil_portugues.cache.adapter.memcached_adapter' => $memcachedAdapter,
    'nil_portugues.cache.adapter.sql.mysql_adapter' => $mysqlAdapter,
    'nil_portugues.cache.adapter.sql.postgresql_adapter' => $postgresqlAdapter,
    'nil_portugues.cache.adapter.sql.sqlite_adapter' => $sqliteAdapter,
    'nil_portugues.cache.adapter.sql.sphinx_adapter' => $sphinxAdapter,
    'nil_portugues.cache.adapter.sql.elastic_adapter' => $elasticSearchAdapter,
    'nil_portugues.user_cache' => new Cache($nativeRedisAdapter, 'user'),
    'nil_portugues.image_cache' => new Cache($nativeRedisAdapter, 'image'),
];
