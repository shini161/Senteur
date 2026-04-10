#!/bin/sh
set -e

# Prepare writable upload directories before PHP-FPM starts so both seeded demo
# assets and runtime uploads are available from the mounted volume.
mkdir -p /var/www/public/uploads/products
mkdir -p /var/www/public/uploads/notes

if [ -d /var/www/resources/demo/uploads/products ]; then
  # Seed demo product images only when the target file does not already exist.
  cp -n /var/www/resources/demo/uploads/products/* /var/www/public/uploads/products/ 2>/dev/null || true
fi

if [ -d /var/www/resources/demo/uploads/notes ]; then
  # Note images follow the same copy-once behavior for local development.
  cp -n /var/www/resources/demo/uploads/notes/* /var/www/public/uploads/notes/ 2>/dev/null || true
fi

# Keep uploads writable for the PHP-FPM worker user inside the container.
chown -R www-data:www-data /var/www/public/uploads
chmod -R 775 /var/www/public/uploads

exec php-fpm
