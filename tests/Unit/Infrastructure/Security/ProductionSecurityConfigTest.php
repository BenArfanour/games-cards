<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProductionKeepsPlaintextHasherForRawApiPasswordSecret(): void
    {
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');
        self::assertIsArray($config);

        $securityConfig = $config['security'] ?? null;
        self::assertIsArray($securityConfig);

        $passwordHashers = $securityConfig['password_hashers'] ?? null;
        self::assertIsArray($passwordHashers);

        $hasherConfig = $passwordHashers[PasswordAuthenticatedUserInterface::class] ?? null;

        self::assertIsArray($hasherConfig);
        self::assertSame('plaintext', $hasherConfig['algorithm'] ?? null);

        $prodConfig = $config['when@prod'] ?? null;

        if (null === $prodConfig) {
            self::addToAssertionCount(1);

            return;
        }

        self::assertIsArray($prodConfig);

        $prodSecurityConfig = $prodConfig['security'] ?? null;

        if (null === $prodSecurityConfig) {
            self::addToAssertionCount(1);

            return;
        }

        self::assertIsArray($prodSecurityConfig);

        $prodPasswordHashers = $prodSecurityConfig['password_hashers'] ?? null;

        if (null === $prodPasswordHashers) {
            self::addToAssertionCount(1);

            return;
        }

        self::assertIsArray($prodPasswordHashers);

        $prodHasherConfig = $prodPasswordHashers[PasswordAuthenticatedUserInterface::class] ?? null;

        if (null === $prodHasherConfig) {
            self::addToAssertionCount(1);

            return;
        }

        $prodAlgorithm = \is_array($prodHasherConfig)
            ? ($prodHasherConfig['algorithm'] ?? null)
            : $prodHasherConfig;

        self::assertSame(
            'plaintext',
            $prodAlgorithm,
            'API_PASSWORD is configured as a raw environment secret, so prod must not expect a pre-hashed value.',
        );
    }
}
