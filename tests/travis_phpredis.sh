#!/bin/bash

wget https://github.com/nicolasff/phpredis/archive/master.zip
unzip master.zip
cd phpredis-master
phpize
./configure
make
sudo make install
phpenv config-add php_extensions.ini
