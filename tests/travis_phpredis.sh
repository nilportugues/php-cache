#!/bin/bash

wget https://github.com/nicolasff/phpredis/archive/master.zip
unzip master.zip
cd phpredis-master
phpize
./configure
make
sudo make install
echo "extension = redis.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
