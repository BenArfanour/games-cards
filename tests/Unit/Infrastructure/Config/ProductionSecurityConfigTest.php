<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProductionKeepsPlaintextHasherForEnvironmentPassword(): void
    {
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');
        self::assertIsArray($config);

        $security = $config['security'] ?? null;
        self::assertIsArray($security);

        $passwordHashers = $security['password_hashers'] ?? null;
        self::assertIsArray($passwordHashers);
        self::assertArrayHasKey(PasswordAuthenticatedUserInterface::class, $passwordHashers);

        self::assertSame(
            ['algorithm' => 'plaintext'],
            $passwordHashers[PasswordAuthenticatedUserInterface::class],
        );

        $prodConfig = $config['when@prod'] ?? [];
        self::assertIsArray($prodConfig);

        $prodSecurity = $prodConfig['security'] ?? [];
        self::assertIsArray($prodSecurity);

        self::assertArrayNotHasKey(
            'password_hashers',
            $prodSecurity,
            'API_PASSWORD is provided as plaintext, so prod must not expect a pre-hashed memory user password.',
        );
    }
}
