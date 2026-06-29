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
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');
        $hasherConfig = $config['security']['password_hashers'][PasswordAuthenticatedUserInterface::class] ?? null;
        $productionHasherConfig = $config['when@prod']['security']['password_hashers'][PasswordAuthenticatedUserInterface::class]
            ?? $hasherConfig;

        self::assertSame(
            '%env(API_PASSWORD)%',
            $config['security']['providers']['api_users']['memory']['users']['api_user']['password'] ?? null,
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
    private static function normalizeHasherConfig(mixed $hasherConfig): array
    {
        if (is_string($hasherConfig)) {
            return ['algorithm' => $hasherConfig];
        }

        self::assertIsArray($hasherConfig);

        return $hasherConfig;
    }
}
