<?php

declare(strict_types=1);

namespace App\Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class SecurityConfigTest extends TestCase
{
    public function testProductionUsesPlaintextHasherForEnvBackedApiPassword(): void
    {
        $config = Yaml::parseFile(__DIR__.'/../../../config/packages/security.yaml');

        self::assertSame(
            '%env(API_PASSWORD)%',
            $config['security']['providers']['api_users']['memory']['users']['api_user']['password'] ?? null,
        );
        self::assertSame(
            'plaintext',
            $config['security']['password_hashers']['Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface']['algorithm'] ?? null,
        );
        self::assertArrayNotHasKey(
            'password_hashers',
            $config['when@prod']['security'] ?? [],
            'The production environment must not replace the plaintext hasher while API_PASSWORD is stored as a raw env value.',
        );
    }
}
