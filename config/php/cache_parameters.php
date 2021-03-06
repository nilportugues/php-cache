<?php

return [
    'filesystem' => [
        'path' => '/tmp'
    ],
    'redis'    => [
        'cache1 ' => [
            'alias'    => 'cache1',
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'database' => 1,
            'timeout'  => 1
        ],
    ],
    'mysql' => [
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
    'postgresql' => [
        'cache_table' => '__cache',
        'connections' => [
            'user'     => 'root',
            'password' => '',
            'database' => [
                'dbname' => 'my_database',
                'host'   => 'localhost',
                'port'   => 5432,
            ],
        ],
    ],
    'sqlite' => [
        'cache_table' => 'cache',
        'connections' => [
            'user'     => '',
            'password' => '',
            'database' => [
                'path' => 'cache.db'
            ],
        ],
    ],
    'sphinx' => [
        'cache_table' => 'cache',
        'connections' => [
            'user'     => '',
            'password' => '',
            'database' => [
                'dbname' => '',
                'host'   => '127.0.0.1',
                'port'   => 9306,
            ],
        ],
    ],    
    'memcached' => [
        'persistent_id' => '__cache',
        'connections' => [
            'server1' => [
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 1
            ],
        ],
    ],
    'elastic' => [
        'base_url' => 'http://localhost:8000',
        'index_name' => 'cache'
    ]
];
