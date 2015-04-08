<?php
use NilPortugues\Cache\Adapter\InMemoryAdapter;

include 'vendor/autoload.php';

$inMemoryAdapter = new InMemoryAdapter();

/** MySQL TEST
$connection = [
    'user'     => 'root',
    'password' => '',
    'database' => [
        'dbname' => 'userData',
        'host'   => "127.0.0.1",
        'port'   => 3306,
    ],
];
$mysqlAdapter = new \NilPortugues\Cache\Adapter\MySqlAdapter($connection, '__cache', $inMemoryAdapter);

$dateTime = new \DateTime();
$mysqlAdapter->set('cache.this', $dateTime);

$cachedDateTime = $mysqlAdapter->get('cache.this');
var_dump($cachedDateTime);
var_dump($cachedDateTime->format('Y-m-d H:i:s') === $dateTime->format('Y-m-d H:i:s'));

$mysqlAdapter->delete('cache.this');

$cachedDateTime = $mysqlAdapter->get('cache.this');
var_dump($cachedDateTime);
 */



/** Predis TEST
$connection = [
    'cache1 ' => [
        'alias'    => 'cache1',
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'database' => 1,
        'timeout'  => 1
    ],
];

$predisAdapter = new \NilPortugues\Cache\Adapter\PredisAdapter($connection, $inMemoryAdapter);

$dateTime = new \DateTime();
$predisAdapter->set('cache.this', $dateTime);

$cachedDateTime = $predisAdapter->get('cache.this');
var_dump($cachedDateTime);
var_dump($cachedDateTime->format('Y-m-d H:i:s') === $dateTime->format('Y-m-d H:i:s'));

$predisAdapter->delete('cache.this');

$cachedDateTime = $predisAdapter->get('cache.this');
var_dump($cachedDateTime);
 */



/** Redis TEST
$connection = [
    'cache1 ' => [
        'alias'    => 'cache1',
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'database' => 1,
        'timeout'  => 1
    ],
];

$redisAdapter = new \NilPortugues\Cache\Adapter\RedisAdapter($connection, $inMemoryAdapter);

$dateTime = new \DateTime();
$redisAdapter->set('cache.this', $dateTime);

$cachedDateTime = $redisAdapter->get('cache.this');
var_dump($cachedDateTime);
var_dump($cachedDateTime->format('Y-m-d H:i:s') === $dateTime->format('Y-m-d H:i:s'));

$redisAdapter->delete('cache.this');

$cachedDateTime = $redisAdapter->get('cache.this');
var_dump($cachedDateTime);
 */

