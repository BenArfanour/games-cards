<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProductionDoesNotOverrideApiPasswordHasher(): void
    {
        $config = self::yamlFile('config/packages/security.yaml');
        $security = self::arrayValue($config, 'security');
        $passwordHashers = self::arrayValue($security, 'password_hashers');
        $apiPasswordHasher = self::arrayValue($passwordHashers, PasswordAuthenticatedUserInterface::class);

        self::assertSame('plaintext', $apiPasswordHasher['algorithm'] ?? null);

        $productionConfig = $config['when@prod'] ?? null;
        if (null === $productionConfig) {
            self::assertTrue(true);

            return;
        }

        self::assertIsArray($productionConfig);

        $productionSecurity = $productionConfig['security'] ?? null;
        if (null === $productionSecurity) {
            self::assertTrue(true);

            return;
        }

        self::assertIsArray($productionSecurity);
        self::assertArrayNotHasKey(
            'password_hashers',
            $productionSecurity,
            'API_PASSWORD is an env-backed plaintext in-memory password, so prod must not switch it to hashed-password verification.',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private static function yamlFile(string $relativePath): array
    {
        $parsed = Yaml::parseFile(dirname(__DIR__, 4).'/'.$relativePath);

        self::assertIsArray($parsed);

        /** @var array<string, mixed> $parsed */
        return $parsed;
    }

    /**
     * @param array<string, mixed> $source
     *
     * @return array<string, mixed>
     */
    private static function arrayValue(array $source, string $key): array
    {
        $value = $source[$key] ?? null;

        self::assertIsArray($value);

        /** @var array<string, mixed> $value */
        return $value;
    }
}
