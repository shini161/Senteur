#!/bin/sh
set -e

mkdir -p /var/www/public/uploads/products
mkdir -p /var/www/public/uploads/notes

if [ -d /var/www/resources/demo/uploads/products ]; then
  cp -n /var/www/resources/demo/uploads/products/* /var/www/public/uploads/products/ 2>/dev/null || true
fi

if [ -d /var/www/resources/demo/uploads/notes ]; then
  cp -n /var/www/resources/demo/uploads/notes/* /var/www/public/uploads/notes/ 2>/dev/null || true
fi

chown -R www-data:www-data /var/www/public/uploads
chmod -R 775 /var/www/public/uploads

exec php-fpm