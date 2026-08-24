<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProductionDoesNotOverridePlaintextHasherForEnvPassword(): void
    {
        $securityConfig = $this->parseSecurityConfig();
        $userHasher = $this->nestedArray(
            $securityConfig,
            'security',
            'password_hashers',
            PasswordAuthenticatedUserInterface::class,
        );

        self::assertSame(['algorithm' => 'plaintext'], $userHasher);
        self::assertSame(
            '%env(API_PASSWORD)%',
            $this->nestedValue(
                $securityConfig,
                'security',
                'providers',
                'api_users',
                'memory',
                'users',
                'api_user',
                'password',
            ),
        );
        self::assertArrayNotHasKey('when@prod', $securityConfig);
    }

    /**
     * @return array<string, mixed>
     */
    private function parseSecurityConfig(): array
    {
        $securityConfig = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');

        if (!\is_array($securityConfig)) {
            self::fail('Security config must parse to an array.');
        }

        /* @var array<string, mixed> $securityConfig */
        return $securityConfig;
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    private function nestedArray(array $config, string ...$keys): array
    {
        $value = $this->nestedValue($config, ...$keys);

        if (!\is_array($value)) {
            self::fail(sprintf('Expected "%s" to be an array.', implode('.', $keys)));
        }

        /* @var array<string, mixed> $value */
        return $value;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function nestedValue(array $config, string ...$keys): mixed
    {
        $value = $config;

        foreach ($keys as $key) {
            if (!\is_array($value) || !array_key_exists($key, $value)) {
                self::fail(sprintf('Missing config key "%s".', implode('.', $keys)));
            }

            $value = $value[$key];
        }

        return $value;
    }
}
