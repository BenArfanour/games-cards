<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure;

use PHPUnit\Framework\TestCase;

final class NginxDefaultConfigTest extends TestCase
{
    public function testPhpFpmReceivesAuthorizationHeader(): void
    {
        $config = file_get_contents(__DIR__.'/../../../docker/nginx/default.conf');

        self::assertIsString($config);
        self::assertStringContainsString(
            'fastcgi_param HTTP_AUTHORIZATION $http_authorization;',
            $config,
            'JWT-protected routes need nginx to pass the Authorization header to PHP-FPM.'
        );
    }
}
