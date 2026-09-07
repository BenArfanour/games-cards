FROM php:8.2.28-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git=1:2.39.5-0+deb12u3 \
    unzip=6.0-28 \
    libicu-dev=72.1-3+deb12u1 \
    libzip-dev=1.7.3-1+b1 \
    libxml2-dev=2.9.14+dfsg-1.3~deb12u6 \
    libonig-dev=6.9.8-1 \
    libsqlite3-dev=3.40.1-2+deb12u2 \
 && docker-php-ext-install intl zip opcache pdo_sqlite \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN { \
  echo "opcache.enable=1"; \
  echo "opcache.enable_cli=1"; \
  echo "opcache.validate_timestamps=1"; \
} > /usr/local/etc/php/conf.d/opcache.ini

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
RUN chown -R www-data:www-data /var/www

USER www-data

CMD ["php-fpm"]
