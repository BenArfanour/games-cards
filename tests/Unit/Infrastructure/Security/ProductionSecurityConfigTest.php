<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProductionUsesPlaintextHasherForDocumentedEnvironmentPassword(): void
    {
        /** @var array<string, mixed> $config */
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');

        $apiUserPassword = $config['security']['providers']['api_users']['memory']['users']['api_user']['password'] ?? null;
        self::assertSame('%env(API_PASSWORD)%', $apiUserPassword);

        /** @var array<string, mixed>|null $productionSecurity */
        $productionSecurity = $config['when@prod']['security'] ?? null;
        $productionHasher = $productionSecurity['password_hashers'][PasswordAuthenticatedUserInterface::class] ?? null;

        self::assertNotSame(
            'auto',
            $productionHasher,
            'The in-memory API user stores API_PASSWORD as a raw environment value, so prod must not require a hashed password.',
        );
    }
}
