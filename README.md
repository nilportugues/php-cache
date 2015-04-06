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

$params = [
  'redis_servers' => [
      ['host' =>'127.0.0.1', 'port'=> 6379, 'database'=> 1, 'alias'=> 'cache1'],
  ],
];

//Array acting as a Service Container
$services = [];

$services['in_memory_adapter'] = new InMemoryAdapter();
$services['predis_adapter'] = new PredisAdapter($params['redis_servers'], $services['in_memory_adapter']);
$services['cache'] = new Cache($services['predis_adapter'], 'namespaced.cache');

return $services;
```

#### 3.2. Usage


### 4. Other configurations

#### 4.1 ElasticSearch as cache

##### Enabling TTL expire every second
It is important that you configure your ElasticSearch by appending the following line to the elasticsearch.yml file:

```yml
indices.ttl.interval: 1s
```

Now restart the ElasticSearch daemon.

If you're wondering where the cache index definition is, the creation of index is handled by the adapter on instantiation if it does not already exist.

#### 4.2 Sphinx as cache

#### 4.3 MySQL as cache

#### 4.4 Postgres as cache

#### 4.5 Sqlite as cache


### 5. Quality

To run the PHPUnit tests at the command line, go to the tests directory and issue phpunit.

This library attempts to comply with PSR-1, PSR-2, and PSR-4. If you notice compliance oversights, please send a patch via pull request.

### 6. Author
Nil Portugués Calderó

 - <contact@nilportugues.com>
 - [http://nilportugues.com](http://nilportugues.com)

### 7. License
The code base is licensed under the MIT license.

