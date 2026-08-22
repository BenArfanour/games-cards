<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProductionDoesNotOverridePlaintextApiPasswordHasher(): void
    {
        /** @var array<string, mixed> $config */
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');

        self::assertSame(
            'plaintext',
            $config['security']['password_hashers'][PasswordAuthenticatedUserInterface::class]['algorithm'] ?? null,
            'The in-memory API user stores API_PASSWORD as a raw env value, so login requires the plaintext hasher.',
        );

        self::assertArrayNotHasKey(
            'password_hashers',
            $config['when@prod']['security'] ?? [],
            'Production must not switch the raw env-backed API password to hashed verification.',
        );
    }
}
