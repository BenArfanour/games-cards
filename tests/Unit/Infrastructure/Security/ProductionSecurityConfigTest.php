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
        /** @var array<string, mixed> $config */
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');
        $hasherConfig = $config['security']['password_hashers'][PasswordAuthenticatedUserInterface::class] ?? null;

        self::assertIsArray($hasherConfig);
        self::assertSame('plaintext', $hasherConfig['algorithm'] ?? null);

        $prodHasherConfig = $config['when@prod']['security']['password_hashers'][PasswordAuthenticatedUserInterface::class] ?? null;

        if ($prodHasherConfig === null) {
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
