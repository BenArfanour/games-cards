<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class SecurityConfigurationTest extends TestCase
{
    public function testProductionDoesNotOverridePlaintextApiPasswordHasher(): void
    {
        /** @var array<string, mixed> $configuration */
        $configuration = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');

        /** @var array<string, mixed> $passwordHasher */
        $passwordHasher = $configuration['security']['password_hashers'][PasswordAuthenticatedUserInterface::class];

        self::assertSame('plaintext', $passwordHasher['algorithm']);
        self::assertArrayNotHasKey(
            'when@prod',
            $configuration,
            'Production login uses API_PASSWORD from the memory provider; changing this requires a hashed provider secret too.'
        );
    }
}
