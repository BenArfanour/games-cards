<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProductionApiUserUsesHashedPassword(): void
    {
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');

        self::assertSame(
            'auto',
            $config['when@prod']['security']['password_hashers'][PasswordAuthenticatedUserInterface::class] ?? null,
        );
        self::assertSame(
            '%env(API_PASSWORD_HASH)%',
            $config['when@prod']['security']['providers']['api_users']['memory']['users']['api_user']['password'] ?? null,
        );
        self::assertSame(
            ['ROLE_API'],
            $config['when@prod']['security']['providers']['api_users']['memory']['users']['api_user']['roles'] ?? null,
        );
    }
}
