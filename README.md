# Cache layer
Cache layer for PHP applications using the on the Chain of Responsability pattern.

### 1. Installation

The recommended way to install the Domain-driven design foundation package is through [Composer](http://getcomposer.org). Run the following command to install it:

```sh
php composer.phar require nilportugues/cache
```

### 2. Drivers Available
- InMemory
- FileSystem
- SQL
- SphinxQL
- Sqlite
- MongoDB
- ElasticSearch
- Memcached
- Redis
  - Native (php5-redis)
  - Client (Predis)

### 3. Use case

- **1st level cache**: Redis (PredisAdapter) is our main cache, in a dedicated server.
- **2nd level cache**: Memcached (MemcachedAdapter) as fallback mechanism, available in the same machine as our PHP script.
- **Application cache**: InMemoryAdapter, used to avoid hiting the external caches on repeated operations and is shared by all cache layers.

The more cache levels the slower the cache system will be, so leverage the cache to your needs. Maybe you don't need a fallback mechanism at all!


#### 3.1. Configuration

Using a Service Container, such as Symfony2's or a simple array of services, define the chain:

```php
<?php
use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\Adapter\Redis\PredisAdapter;
use NilPortugues\Cache\Cache;

$parameters = [
  'redis_servers' => [
      ['host' =>'127.0.0.1', 'port'=> 6379, 'database'=> 1, 'alias'=> 'cache1'],
  ],
];

//Array acting as a Service Container
$services = [];

$services['cache.adapter.in_memory_adapter'] = new InMemoryAdapter();

$services['cache.adapter.redis.predis_adapter'] = new PredisAdapter(
    $parameters['redis_servers'],
    $services['cache.adapter.in_memory_adapter']
);

$services['cache'] = new Cache(
    $services['cache.adapter.redis.predis_adapter'],
    'namespaced.cache'
);


return $services;
```

#### 3.2. Usage


### 4. Quality

To run the PHPUnit tests at the command line, go to the tests directory and issue phpunit.

This library attempts to comply with PSR-1, PSR-2, and PSR-4. If you notice compliance oversights, please send a patch via pull request.

### 5. Author [↑](#index_block)
Nil Portugués Calderó

 - <contact@nilportugues.com>
 - [http://nilportugues.com](http://nilportugues.com)

### 6. License [↑](#index_block)
The code base is licensed under the MIT license.

