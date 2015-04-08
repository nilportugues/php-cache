<?php

return [
    'redis_servers'    => [
        'cache1 ' => [
            'alias'    => 'cache1',
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'database' => 1,
            'timeout'  => 1
        ],
    ],
    'mysql_servers' => [
        'cache_table' => '__cache',
        'connections' => [
            'user'     => 'root',
            'password' => '',
            'database' => [
                'dbname' => 'my_database',
                'host'   => 'localhost',
                'port'   => 3306,
            ],
        ],
    ],
    'sphinx_servers' => [
        'index_name' => 'cache',
        'connections' => [
            'user'     => '',
            'password' => '',
            'database' => [
                'dbname' => 'cache',
                'host'   => '127.0.0.1',
                'port'   => 9306,
            ],
        ],
    ],    
    'memcached_servers' => [
        'persistent_id' => '__cache',
        'connections' => [
            'server1' => [
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 1
            ],
        ],
    ]
];
