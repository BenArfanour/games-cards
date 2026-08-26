<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProductionDoesNotOverrideApiPasswordHasher(): void
    {
        $config = Yaml::parseFile(__DIR__ . '/../../../config/packages/security.yaml');

        self::assertIsArray($config);

        $security = $config['security'] ?? null;
        self::assertIsArray($security);

        $passwordHashers = $security['password_hashers'] ?? null;
        self::assertIsArray($passwordHashers);

        $apiPasswordHasher = $passwordHashers[PasswordAuthenticatedUserInterface::class] ?? null;
        self::assertIsArray($apiPasswordHasher);

        self::assertSame('plaintext', $apiPasswordHasher['algorithm'] ?? null);
        self::assertArrayNotHasKey(
            'when@prod',
            $config,
            'Production must not hash the raw API_PASSWORD used by the in-memory API user.',
        );
    }
}
