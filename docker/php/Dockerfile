# Application image for the PHP-FPM service used by Docker Compose.
FROM php:8.3-fpm

WORKDIR /var/www

# Install the minimal OS packages needed for Composer and zip extraction.
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies before copying the whole project to preserve better
# Docker layer caching during repeated local rebuilds.
COPY composer.json composer.lock ./

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

COPY . .

# Pre-create upload directories so mounted volumes inherit the expected layout.
RUN mkdir -p /var/www/public/uploads/products \
    && mkdir -p /var/www/public/uploads/notes \
    && chown -R www-data:www-data /var/www/public/uploads \
    && chmod -R 775 /var/www/public/uploads

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
