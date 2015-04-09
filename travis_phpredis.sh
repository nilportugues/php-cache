#!/bin/bash

wget https://github.com/nicolasff/phpredis/archive/master.zip
unzip master.zip
cd phpredis-master
phpize
./configure
make
sudo make install
sudo echo "extension=redis.so" > /etc/php5/conf.d/redis.ini
