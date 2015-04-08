# Cache layer
Cache layer for PHP applications using the on the Chain of Responsability pattern.

### 1. Installation

The recommended way to install the Domain-driven design foundation package is through [Composer](http://getcomposer.org). Run the following command to install it:

```sh
php composer.phar require nilportugues/cache
```
### 2. Features

### 3. Drivers Available
The package provides several implementations for a key-value cache. 

The most sensitive choice is to make use of the Cache based adapters such as Redis and Memcached, which are the most performant and specialized.

Yet sometimes these are not available and other options should be considered, but we got you covered.

#### Cache based: 
- **Memcached:** MemcachedAdapter *(php5-memcached)*
- **Redis:** RedisAdapter *(php5-redis)*, PredisAdapter *(Predis)*

#### Full-text based:
- **SphinxQL:** SphinxAdapter
- **ElasticSearch:** ElasticSearchAdapter

#### System based:
- **Memory:** InMemoryAdapter
- **FileSystem:** FileSystemAdapter

#### Database based:
- **MySQL:** MySqlAdapter
- **PostgreSql:** PostgreSqlAdapter
- **Sqlite:** SqliteAdapter

### 4. Usage

**1st level cache**: Redis (PredisAdapter) is our main cache, in a dedicated server.

**2nd level cache**: Memcached (MemcachedAdapter) as fallback mechanism, available in the same machine as our PHP script.

**Application cache**: InMemoryAdapter, used to avoid hiting the external caches on repeated operations and is shared by all cache layers.

The more cache levels the slower the cache system will be, so leverage the cache to your needs. Maybe you don't need a fallback mechanism at all!


#### 4.1. Configuration

Using a Service Container, such as Symfony2's or a simple array of services, define the chain:

```php
<?php
include_once realpath(dirname(__FILE__)).'/../../vendor/autoload.php';

use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\Adapter\MemcachedAdapter;
use NilPortugues\Cache\Adapter\PredisAdapter;
use NilPortugues\Cache\Cache;

$parameters = include_once realpath(dirname(__FILE__)).'/cache_parameters.php';

$inMemoryAdapter = new InMemoryAdapter();

$predisRedisAdapter = new PredisAdapter(
    $parameters['redis_servers'],
    $inMemoryAdapter
);

$memcachedAdapter = new MemcachedAdapter(
    $parameters['memcached_servers']['persistent_id'],
    $parameters['memcached_servers']['connections'],
    $inMemoryAdapter
);

return [
    'cache.adapter.in_memory_adapter' => $inMemoryAdapter,
    'cache.adapter.memcached_adapter' => $memcachedAdapter,
    'cache.adapter.redis.predis_adapter' => $predisRedisAdapter,
    'user_cache' => new Cache($predisRedisAdapter, 'user'),
    'image_cache' => new Cache($predisRedisAdapter, 'image'),
];
```

#### 4.2. Usage


#### 4.3 Other configurations

##### 4.3.1 ElasticSearch as cache

It is important that you configure your ElasticSearch by appending the following line to the **elasticsearch.yml** file:

```yml
indices.ttl.interval: 1s
```

Now restart the ElasticSearch daemon.

If you're wondering where the cache index definition is, the creation of index is handled by the adapter on instantiation if it does not already exist.

##### 4.3.2 Sphinx as cache
Configuration provided in the `/migrations/sphinx.conf` file.

##### 4.3.3 MySQL as cache
Configuration provided in the `/migrations/mysql_schema.sql` file.

##### 4.3.4 Postgres as cache
Configuration provided in the `/migrations/postgresql_schema.sql` file.

##### 4.3.5 Sqlite as cache
Configuration provided in the `/migrations/sqlite_schema.sqlite` file.

### 5. Quality

To run the PHPUnit tests at the command line, go to the tests directory and issue phpunit.

This library attempts to comply with PSR-1, PSR-2, and PSR-4. If you notice compliance oversights, please send a patch via pull request.

### 6. Author
Nil Portugués Calderó

 - <contact@nilportugues.com>
 - [http://nilportugues.com](http://nilportugues.com)

### 7. License
The code base is licensed under the MIT license.

