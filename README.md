 Cache layer
=========================

[![Build Status](https://travis-ci.org/nilportugues/cache.png)](https://travis-ci.org/nilportugues/cache) [![Coverage Status](https://coveralls.io/repos/nilportugues/cache/badge.svg?branch=master)](https://coveralls.io/r/nilportugues/cache?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nilportugues/cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nilportugues/cache/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/918cfa6d-997d-4e41-bdcb-d01970485074/mini.png)](https://insight.sensiolabs.com/projects/918cfa6d-997d-4e41-bdcb-d01970485074) [![Latest Stable Version](https://poser.pugx.org/nilportugues/cache/v/stable.svg)](https://packagist.org/packages/nilportugues/cache) [![Total Downloads](https://poser.pugx.org/nilportugues/cache/downloads.svg)](https://packagist.org/packages/nilportugues/cache) [![License](https://poser.pugx.org/nilportugues/cache/license.svg)](https://packagist.org/packages/nilportugues/cache) 

 
Cache layer for PHP applications capable of being used standalone or with the on the Chain of Responsability pattern.



### 1. Installation

The recommended way to install the Domain-driven design foundation package is through [Composer](http://getcomposer.org). Run the following command to install it:

```sh
php composer.phar require nilportugues/cache
```
---

### 2. Features

- One cache class, many adapters.
- All cache adapters can be used as standalone cache classes.
- Opt-in to be used as a chain of caches.
- Implementation normalizes all mechanisms and behaviours.
- Configuration files for all adapters in vanilla PHP and Symfony2 format provided in `config`and `migrations` directories.
- High quality, 100% tested code.

---

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

---


### 4. Example

    The more cache levels the slower the cache system will be, so leverage the 
    cache to your needs. 
    Maybe you don't need a fallback mechanism at all! This is just an example.

**1st level cache**: Redis (PredisAdapter) is our main cache, in a dedicated server.

**2nd level cache**: Memcached (MemcachedAdapter) as fallback mechanism, available in the same machine as our PHP script.

**Application cache**: InMemoryAdapter, used to avoid hiting the external caches on repeated operations and is shared by all cache layers.


#### 4.1. Configuration

Using a Service Container, such as an array returning the services or a more popular solution such as Symfony's Service Container, build the caches.

For this example, we'll be building two caches, **user_cache** and **image_cache**. Both use Predis as first level cache and a fallback to Memcached if Predis cannot establish a connection during runtime.

```php
<?php
include_once realpath(dirname(__FILE__)).'/vendor/autoload.php';

use NilPortugues\Cache\Adapter\InMemoryAdapter;
use NilPortugues\Cache\Adapter\MemcachedAdapter;
use NilPortugues\Cache\Adapter\PredisAdapter;
use NilPortugues\Cache\Cache;

$parameters = include_once realpath(dirname(__FILE__)).'/cache_parameters.php';

//App cache
$inMemoryAdapter = new InMemoryAdapter();

//Second level
$memcachedAdapter = new MemcachedAdapter(
    $parameters['memcached']['persistent_id'],
    $parameters['memcached']['connections'],
    $inMemoryAdapter
);
//First level
$predisRedisAdapter = new PredisAdapter(
    $parameters['redis'],
    $inMemoryAdapter,
    $memcachedAdapter
);

return [
    'user_cache' => new Cache($predisRedisAdapter, 'user', 60*5), //60 seconds cache
    'image_cache' => new Cache($predisRedisAdapter, 'image', 60*60), //1 hour cache
];
```

#### 4.2. Usage

Now, using a Service Container, we'll get the **user_cache** to fetch data, or add if it does not exist. This data will be stored in the caches. 

For fetching, first it's checked if data is available in memory, if not, it's fetched from the data storage, added to the in memory cache and returned to the user.

```php
$db = $this->serviceContainer->get('database');
$userCache = $this->serviceContainer->get('user_cache');

$userId = 1;
$cacheKey = sprintf("user:id:%s", $userId);

$user = $user = $userCache->get($cacheKey);
if(null !== $user) {
  return $user;
}

$user = $db->findById($userId);
$userCache->set($cacheKey, $user);

return $user;
```

 And that's pretty much it. Notice how same key is used for the get and set methods.


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


---


### 5. Quality

To run the PHPUnit tests at the command line, go to the tests directory and issue phpunit.

This library attempts to comply with PSR-1, PSR-2, and PSR-4. If you notice compliance oversights, please send a patch via pull request.


---


### 6. Author
Nil Portugués Calderó

 - <contact@nilportugues.com>
 - [http://nilportugues.com](http://nilportugues.com)


---


### 7. License
The code base is licensed under the MIT license.

