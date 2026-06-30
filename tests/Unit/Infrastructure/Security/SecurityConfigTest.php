<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class SecurityConfigTest extends TestCase
{
    public function testApiUserPasswordHasherMatchesRawEnvironmentPassword(): void
    {
        $config = self::loadSecurityConfig();
        $security = self::arrayConfig($config, 'security');
        $hasherConfig = self::arrayValue(
            self::arrayConfig($security, 'password_hashers'),
            PasswordAuthenticatedUserInterface::class,
        );
        $productionHasherConfig = self::productionHasherConfig($config, $hasherConfig);
        $apiUserConfig = self::arrayConfig(
            self::arrayConfig(
                self::arrayConfig(
                    self::arrayConfig($security, 'providers'),
                    'api_users',
                ),
                'memory',
            ),
            'users',
        );

        self::assertSame(
            '%env(API_PASSWORD)%',
            self::arrayValue(self::arrayConfig($apiUserConfig, 'api_user'), 'password'),
        );
        self::assertSame(['algorithm' => 'plaintext'], $hasherConfig);
        self::assertSame(
            ['algorithm' => 'plaintext'],
            self::normalizeHasherConfig($productionHasherConfig),
            'The API memory user stores a raw env password; a prod auto hasher rejects valid credentials.',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private static function loadSecurityConfig(): array
    {
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');

        self::assertIsArray($config);

        /* @var array<string, mixed> $config */
        return $config;
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    private static function arrayConfig(array $config, string $key): array
    {
        $value = self::arrayValue($config, $key);

        self::assertIsArray($value);

        /* @var array<string, mixed> $value */
        return $value;
    }

    /**
     * @param array<string, mixed> $config
     */
    private static function arrayValue(array $config, string $key): mixed
    {
        self::assertArrayHasKey($key, $config);

        return $config[$key];
    }

    /**
     * @param array<string, mixed> $config
     */
    private static function productionHasherConfig(array $config, mixed $defaultHasherConfig): mixed
    {
        if (!array_key_exists('when@prod', $config)) {
            return $defaultHasherConfig;
        }

        $prodConfig = self::arrayConfig($config, 'when@prod');
        if (!array_key_exists('security', $prodConfig)) {
            return $defaultHasherConfig;
        }

        $prodSecurityConfig = self::arrayConfig($prodConfig, 'security');
        if (!array_key_exists('password_hashers', $prodSecurityConfig)) {
            return $defaultHasherConfig;
        }

        $prodHashersConfig = self::arrayConfig($prodSecurityConfig, 'password_hashers');

        return $prodHashersConfig[PasswordAuthenticatedUserInterface::class] ?? $defaultHasherConfig;
    }

    /**
     * @return array<string, mixed>
     */
    private static function normalizeHasherConfig(mixed $hasherConfig): array
    {
        if (is_string($hasherConfig)) {
            return ['algorithm' => $hasherConfig];
        }

        self::assertIsArray($hasherConfig);

        /* @var array<string, mixed> $hasherConfig */
        return $hasherConfig;
    }
}
