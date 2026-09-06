<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure;

use PHPUnit\Framework\TestCase;

final class DockerPhpExtensionsTest extends TestCase
{
    public function testPhpImageInstallsPostgresPdoDriverForConfiguredDatabaseService(): void
    {
        $compose = file_get_contents(__DIR__.'/../../../docker-compose.yml');
        $dockerfile = file_get_contents(__DIR__.'/../../../docker/php/Dockerfile');

        self::assertIsString($compose);
        self::assertIsString($dockerfile);
        self::assertStringContainsString('image: postgres:', $compose);
        self::assertStringContainsString('libpq-dev', $dockerfile);
        self::assertMatchesRegularExpression('/docker-php-ext-install\s+.*\bpdo_pgsql\b/s', $dockerfile);
    }
}
