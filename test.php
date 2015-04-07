<?php
use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\Adapter\MySqlAdapter;

include 'vendor/autoload.php';

$connection = [
    'user'     => 'root',
    'password' => '',
    'database' => [
        'dbname' => 'userData',
        'host'   => "127.0.0.1",
        'port'   => 3306,
    ],
];

$inMemoryAdapter = new InMemoryAdapter();
$mysqlAdapter = new MySqlAdapter($connection, '__cache', $inMemoryAdapter);
/*

$mysqlAdapter->set('cache.this', $dateTime, 1000);

$cachedDateTime = $mysqlAdapter->get('cache.this');

var_dump($cachedDateTime->format('Y-m-d H:i:s') === $dateTime->format('Y-m-d H:i:s'));*/

//$dateTime = new \DateTime();
//$mysqlAdapter->set('cache.this', $dateTime);
$cachedDateTime = $mysqlAdapter->get('cache.this');
var_dump($cachedDateTime);