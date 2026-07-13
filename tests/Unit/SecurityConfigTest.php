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
        self::assertIsArray($config);

        $security = $config['security'] ?? null;
        self::assertIsArray($security);

        $passwordHashers = $security['password_hashers'] ?? null;
        self::assertIsArray($passwordHashers);

        $defaultHasher = $passwordHashers[PasswordAuthenticatedUserInterface::class] ?? null;
        $productionHasher = $defaultHasher;

        $productionConfig = $config['when@prod'] ?? null;
        if (is_array($productionConfig)) {
            $productionSecurity = $productionConfig['security'] ?? null;
            if (is_array($productionSecurity)) {
                $productionPasswordHashers = $productionSecurity['password_hashers'] ?? null;
                if (is_array($productionPasswordHashers)) {
                    $productionHasher = $productionPasswordHashers[PasswordAuthenticatedUserInterface::class] ?? $defaultHasher;
                }
            }
        }

        self::assertSame(['algorithm' => 'plaintext'], $productionHasher);
    }
}
