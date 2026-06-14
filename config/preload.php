<?php

declare(strict_types=1);

/**
 * OPcache preloading entry point (production only).
 *
 * Symfony generates var/cache/prod/App_KernelProdContainer.preload.php after:
 *   APP_ENV=prod composer install --no-dev --optimize-autoloader
 *   php bin/console cache:warmup --env=prod
 *
 * To enable in Docker, add to php opcache.ini (disabled in dev by default):
 *   opcache.preload=/var/www/html/config/preload.php
 *   opcache.preload_user=www-data
 *
 * @see https://symfony.com/doc/current/performance/opcache.html
 */
if (file_exists(dirname(__DIR__).'/var/cache/prod/App_KernelProdContainer.preload.php')) {
    require dirname(__DIR__).'/var/cache/prod/App_KernelProdContainer.preload.php';
}
