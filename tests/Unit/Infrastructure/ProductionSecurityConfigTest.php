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
        $config = Yaml::parseFile(__DIR__.'/../../../config/packages/security.yaml');

        self::assertIsArray($config);

        $security = $config['security'] ?? null;
        self::assertIsArray($security);

        $passwordHashers = $security['password_hashers'] ?? null;
        self::assertIsArray($passwordHashers);

        $rootHasher = $passwordHashers[PasswordAuthenticatedUserInterface::class] ?? null;
        self::assertIsArray($rootHasher);

        $production = $config['when@prod'] ?? [];
        self::assertIsArray($production);

        $productionSecurity = $production['security'] ?? [];
        self::assertIsArray($productionSecurity);

        $productionPasswordHashers = $productionSecurity['password_hashers'] ?? [];
        self::assertIsArray($productionPasswordHashers);

        $productionHasher = $productionPasswordHashers[PasswordAuthenticatedUserInterface::class] ?? null;

        self::assertSame('plaintext', $rootHasher['algorithm'] ?? null);
        self::assertNull(
            $productionHasher,
            'API_PASSWORD is configured as a raw env value, so prod must not switch it to an encoded hasher.',
        );
    }
}
