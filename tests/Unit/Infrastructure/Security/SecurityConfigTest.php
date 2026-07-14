<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class SecurityConfigTest extends TestCase
{
    public function testApiPasswordHasherStaysPlaintextForEnvBackedPassword(): void
    {
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');

        self::assertSame(
            'plaintext',
            $config['security']['password_hashers']['Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface']['algorithm'],
        );

        self::assertSame(
            '%env(API_PASSWORD)%',
            $config['security']['providers']['api_users']['memory']['users']['api_user']['password'],
        );

        self::assertArrayNotHasKey(
            'when@prod',
            $config,
            'The raw API_PASSWORD provider value must not be verified with the prod auto hasher.',
        );
    }
}
