<?php

declare(strict_types=1);

namespace App\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class SecurityConfigTest extends TestCase
{
    public function testProdKeepsApiPasswordHasherCompatibleWithPlaintextSecret(): void
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

        $prodConfig = $config['when@prod'] ?? null;
        if (!is_array($prodConfig)) {
            return;
        }

        $prodSecurity = $prodConfig['security'] ?? null;
        if (!is_array($prodSecurity)) {
            return;
        }

        $prodPasswordHashers = $prodSecurity['password_hashers'] ?? null;
        if (
            !is_array($prodPasswordHashers)
            || !array_key_exists(PasswordAuthenticatedUserInterface::class, $prodPasswordHashers)
        ) {
            return;
        }

        self::assertPasswordHasherUsesPlaintext(
            $prodPasswordHashers[PasswordAuthenticatedUserInterface::class],
        );
    }

    private static function assertPasswordHasherUsesPlaintext(mixed $passwordHasher): void
    {
        if (is_string($passwordHasher)) {
            self::assertSame('plaintext', $passwordHasher);

            return;
        }

        if (!is_array($passwordHasher)) {
            self::fail('Production API password hasher must be a string or structured configuration.');
        }

        self::assertSame('plaintext', $passwordHasher['algorithm'] ?? null);
    }
}
