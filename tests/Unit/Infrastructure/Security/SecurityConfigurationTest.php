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
        $configuration = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');
        self::assertIsArray($configuration);

        $security = $configuration['security'] ?? null;
        self::assertIsArray($security);

        $passwordHashers = $security['password_hashers'] ?? null;
        self::assertIsArray($passwordHashers);

        $passwordHasher = $passwordHashers[PasswordAuthenticatedUserInterface::class] ?? null;
        self::assertIsArray($passwordHasher);

        self::assertSame('plaintext', $passwordHasher['algorithm']);
        self::assertArrayNotHasKey(
            'when@prod',
            $configuration,
            'Production login uses API_PASSWORD from the memory provider; changing this requires a hashed provider secret too.'
        );
    }
}
