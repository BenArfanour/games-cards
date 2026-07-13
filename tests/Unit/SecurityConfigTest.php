<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class SecurityConfigTest extends TestCase
{
    public function testProductionUsesHasherCompatibleWithRawApiPassword(): void
    {
        $config = Yaml::parseFile(__DIR__.'/../../config/packages/security.yaml');

        $defaultHasher = $config['security']['password_hashers'][PasswordAuthenticatedUserInterface::class] ?? null;
        $productionHasher = $config['when@prod']['security']['password_hashers'][PasswordAuthenticatedUserInterface::class] ?? $defaultHasher;

        self::assertSame(['algorithm' => 'plaintext'], $productionHasher);
    }
}
