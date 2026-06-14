FROM php:8.2-fpm

# System deps
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libxml2-dev libonig-dev \
 && docker-php-ext-install intl zip opcache pdo pdo_mysql \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Opcache (prod-ish)
RUN { \
  echo "opcache.enable=1"; \
  echo "opcache.enable_cli=1"; \
  echo "opcache.validate_timestamps=0"; \
} > /usr/local/etc/php/conf.d/opcache.ini

# Permissions (pour WSL/host)
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
RUN chown -R www-data:www-data /var/www

USER www-data

CMD ["php-fpm"]
