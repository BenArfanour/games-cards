<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class SecurityConfigTest extends TestCase
{
    public function testProductionInheritsPlaintextHasherForEnvPassword(): void
    {
        $config = self::parseSecurityConfig();

        $baseHasher = self::nestedValue($config, [
            'security',
            'password_hashers',
            PasswordAuthenticatedUserInterface::class,
        ]);

        if (!is_array($baseHasher)) {
            self::fail('The API password hasher must be configured.');
        }

        self::assertSame('plaintext', $baseHasher['algorithm'] ?? null);
        self::assertNull(
            self::nestedValue($config, [
                'when@prod',
                'security',
                'password_hashers',
                PasswordAuthenticatedUserInterface::class,
            ]),
            'Production must inherit the plaintext hasher because API_PASSWORD is stored as a raw env value.',
        );
    }

    /**
     * @return array<string, mixed>
     */
    private static function parseSecurityConfig(): array
    {
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');

        if (!is_array($config)) {
            self::fail('Security config must parse to an array.');
        }

        /* @var array<string, mixed> $config */
        return $config;
    }

    /**
     * @param array<string, mixed> $data
     * @param list<string>         $path
     */
    private static function nestedValue(array $data, array $path): mixed
    {
        $current = $data;

        foreach ($path as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return null;
            }

            $current = $current[$key];
        }

        return $current;
    }
}
