<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProductionKeepsPlaintextHasherForEnvironmentPassword(): void
    {
        $config = Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml');

        self::assertSame(
            ['algorithm' => 'plaintext'],
            $config['security']['password_hashers']['Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface'],
        );
        self::assertArrayNotHasKey(
            'when@prod',
            $config,
            'API_PASSWORD is provided as plaintext, so prod must not expect a pre-hashed memory user password.',
        );
    }
}
