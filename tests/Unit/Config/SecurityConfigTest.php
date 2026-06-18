<?php

declare(strict_types=1);

namespace App\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class SecurityConfigTest extends TestCase
{
    public function testProdDoesNotOverridePlaintextApiPasswordHasher(): void
    {
        $config = Yaml::parseFile(__DIR__.'/../../../config/packages/security.yaml');
        if (!is_array($config)) {
            self::fail('Security configuration must parse as an array.');
        }

        $security = $config['security'] ?? null;
        if (!is_array($security)) {
            self::fail('Security configuration must define a security section.');
        }

        $passwordHashers = $security['password_hashers'] ?? null;
        if (!is_array($passwordHashers)) {
            self::fail('Security configuration must define password hashers.');
        }

        $apiPasswordHasher = $passwordHashers[PasswordAuthenticatedUserInterface::class] ?? null;
        if (!is_array($apiPasswordHasher)) {
            self::fail('API password hasher must use structured configuration.');
        }

        self::assertSame('plaintext', $apiPasswordHasher['algorithm'] ?? null);
        self::assertArrayNotHasKey(
            'when@prod',
            $config,
            'Production must not override the plaintext API_PASSWORD hasher.',
        );
    }
}
