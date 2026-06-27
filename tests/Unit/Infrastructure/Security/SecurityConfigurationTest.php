<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class SecurityConfigurationTest extends TestCase
{
    public function testProductionKeepsEnvBackedApiPasswordCompatibleWithPlaintextSecret(): void
    {
        /** @var array<string, mixed> $config */
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');

        self::assertSame(
            '%env(API_PASSWORD)%',
            self::readConfigPath($config, [
                'security',
                'providers',
                'api_users',
                'memory',
                'users',
                'api_user',
                'password',
            ])
        );

        $rootHasher = self::readConfigPath($config, [
            'security',
            'password_hashers',
            'Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface',
        ]);

        self::assertSame(
            ['algorithm' => 'plaintext'],
            $rootHasher,
            'The env-backed in-memory API password requires a plaintext-compatible root hasher.'
        );

        $productionHasher = self::readConfigPath($config, [
            'when@prod',
            'security',
            'password_hashers',
            'Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface',
        ]);

        self::assertTrue(
            null === $productionHasher || ['algorithm' => 'plaintext'] === $productionHasher,
            'Production must not switch the env-backed in-memory API password to a hash-only verifier.'
        );
    }

    /**
     * @param array<string, mixed> $config
     * @param list<string>         $path
     */
    private static function readConfigPath(array $config, array $path): mixed
    {
        $current = $config;

        foreach ($path as $segment) {
            if (!\is_array($current) || !\array_key_exists($segment, $current)) {
                return null;
            }

            $current = $current[$segment];
        }

        return $current;
    }
}
