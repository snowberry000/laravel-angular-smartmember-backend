#!/bin/bash


service httpd restart
service mysqld restart

cd /var/www/html/api/
./init.sh
chmod -R 777 storage/
php artisan key:generate




