<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProdDoesNotOverridePlaintextHasherForEnvBackedMemoryUser(): void
    {
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');

        self::assertIsArray($config);
        /** @var array{
         *     security: array{
         *         password_hashers: array<class-string, array{algorithm: string}|string>,
         *         providers: array{api_users: array{memory: array{users: array{api_user: array{password: string}}}}},
         *     },
         *     'when@prod'?: mixed,
         * } $config
         */
        self::assertSame(
            '%env(API_PASSWORD)%',
            $config['security']['providers']['api_users']['memory']['users']['api_user']['password'] ?? null,
        );
        self::assertSame(
            ['algorithm' => 'plaintext'],
            $config['security']['password_hashers'][PasswordAuthenticatedUserInterface::class] ?? null,
        );
        self::assertArrayNotHasKey(
            'when@prod',
            $config,
            'The env-backed memory user stores a raw API_PASSWORD, so prod must not switch to hashed verification.',
        );
    }
}
