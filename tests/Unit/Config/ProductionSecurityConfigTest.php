<?php

declare(strict_types=1);

namespace App\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProductionKeepsPlaintextHasherForEnvBackedApiUser(): void
    {
        $config = Yaml::parseFile(__DIR__.'/../../../config/packages/security.yaml');
        self::assertIsArray($config);

        $security = $config['security'] ?? null;
        self::assertIsArray($security);

        $passwordHashers = $security['password_hashers'] ?? null;
        self::assertIsArray($passwordHashers);

        $apiUserHasher = $passwordHashers[PasswordAuthenticatedUserInterface::class] ?? null;
        self::assertIsArray($apiUserHasher);

        $providers = $security['providers'] ?? null;
        self::assertIsArray($providers);

        $apiUsersProvider = $providers['api_users'] ?? null;
        self::assertIsArray($apiUsersProvider);

        $memoryProvider = $apiUsersProvider['memory'] ?? null;
        self::assertIsArray($memoryProvider);

        $users = $memoryProvider['users'] ?? null;
        self::assertIsArray($users);

        $apiUser = $users['api_user'] ?? null;
        self::assertIsArray($apiUser);

        self::assertSame(
            'plaintext',
            $apiUserHasher['algorithm'] ?? null,
        );
        self::assertSame(
            '%env(API_PASSWORD)%',
            $apiUser['password'] ?? null,
        );
        self::assertArrayNotHasKey(
            'when@prod',
            $config,
            'The env-backed API user stores a raw password, so prod must not override the plaintext hasher.',
        );
    }
}
