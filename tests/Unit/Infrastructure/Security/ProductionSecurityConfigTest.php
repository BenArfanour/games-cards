<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProductionApiUserUsesHashedPassword(): void
    {
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');
        self::assertIsArray($config);

        /** @var array<string, mixed> $config */
        $prodConfig = self::arrayValue($config, 'when@prod');
        $security = self::arrayValue($prodConfig, 'security');
        $passwordHashers = self::arrayValue($security, 'password_hashers');
        $providers = self::arrayValue($security, 'providers');
        $apiUsersProvider = self::arrayValue($providers, 'api_users');
        $memoryProvider = self::arrayValue($apiUsersProvider, 'memory');
        $users = self::arrayValue($memoryProvider, 'users');
        $apiUser = self::arrayValue($users, 'api_user');

        self::assertSame(
            'auto',
            $passwordHashers[PasswordAuthenticatedUserInterface::class] ?? null,
        );
        self::assertSame(
            '%env(API_PASSWORD_HASH)%',
            $apiUser['password'] ?? null,
        );
        self::assertSame(
            ['ROLE_API'],
            $apiUser['roles'] ?? null,
        );
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    private static function arrayValue(array $config, string $key): array
    {
        self::assertArrayHasKey($key, $config);
        self::assertIsArray($config[$key]);

        /** @var array<string, mixed> $value */
        $value = $config[$key];

        return $value;
    }
}
