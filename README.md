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

Lets imagine we decided to use Redis as our main cache, being a dedicated server. This is our main cache.

As a fallback mechanism, lets suppose we decided to use Memcached, which is available to us in the same machine as our PHP script. 

Finally, an application level cache, the InMemoryAdapter is used to avoid hiting the external caches on repeated operations.

- **1st level cache**: Redis (PredisAdapter)
- **2nd level cache**: Memcached (MemcachedAdapter)
- **Application cache**: InMemoryAdapter

#### 3.1. Configuration 

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

