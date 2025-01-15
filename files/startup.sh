#!/bin/bash

set -x
set -e

# if [ -x '/usr/bin/supervisord' ]; then 
#   /usr/bin/supervisord
# fi

[ -x '/usr/sbin/mysqld' ] && [ ! -f '/var/www/sample/data/ibdata1' ] && /usr/bin/mysql_install_db --datadir=/var/www/sample/data

if [ -x '/usr/sbin/mysqld' ]; then
  chown -R mysql:mysql /var/www/sample/data || true
  chown -R mysql:mysql /var/www/sample/src/storage/logs/mysql* || true
  chmod 777 /var/www/sample/data
  /usr/sbin/mysqld --defaults-file=/var/www/sample/conf/mysql.conf &
  sleep 4
  echo "CREATE DATABASE IF NOT EXISTS sample" | mysql
  echo "CREATE USER IF NOT EXISTS 'sample'@'%'" | mysql
  echo "GRANT ALL PRIVILEGES ON *.* TO 'sample'@'%'" | mysql
  echo "GRANT ALL PRIVILEGES ON *.* TO 'sample'@'localhost'" | mysql
  if [ -f '/var/www/sample/src/artisan' ]; then
    yes | php artisan migrate 
  fi
fi

mkdir -p /var/www/sample/src/storage/logs || true
/usr/sbin/php-fpm --fpm-config /var/www/sample/conf/php-fpm.conf

if [ -d '/var/www/sample/src/app' ] && [ ! -d '/var/www/sample/src/vendor' ]; then
  cd /var/www/sample/src
  composer install
fi

chown -R apache:apache /var/www/sample/src/storage || true
chown -R apache:apache /var/www/sample/src/bootstrap || true
chmod -R 777 /var/www/sample/src/storage || true
chmod -R 777 /var/www/sample/src/bootstrap || true

/usr/sbin/httpd -f /var/www/sample/conf/httpd.conf -D FOREGROUND
