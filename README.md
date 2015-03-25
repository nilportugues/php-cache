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

### 3. Usage

#### 3.1. Configuration

#### 3.2. Example


### 4. Recommended Setup

This set up is recommended when having a Redis as your main cache in the same machine or external, as a fallback Memcached to be used for the current machine and final fallback, InMemory that would act as a Registry.

- 1st level cache: Redis
- 2nd level cache: Memcached
- 3rd level cache: InMemory

#### 4.1. Configuration 


### 5. Quality

To run the PHPUnit tests at the command line, go to the tests directory and issue phpunit.

This library attempts to comply with PSR-1, PSR-2, and PSR-4. If you notice compliance oversights, please send a patch via pull request.

### 6. Author [↑](#index_block)
Nil Portugués Calderó

 - <contact@nilportugues.com>
 - [http://nilportugues.com](http://nilportugues.com)

### 7. License [↑](#index_block)
The code base is licensed under the MIT license.

