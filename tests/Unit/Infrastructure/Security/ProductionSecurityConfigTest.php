<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Yaml\Yaml;

final class ProductionSecurityConfigTest extends TestCase
{
    public function testProductionUsesPlaintextHasherForDocumentedEnvironmentPassword(): void
    {
        $config = self::arrayValue(Yaml::parseFile(__DIR__.'/../../../../config/packages/security.yaml'), 'Security config must parse as an array.');

        $security = self::arrayValue($config['security'] ?? null, 'Security config must define a security section.');
        $providers = self::arrayValue($security['providers'] ?? null, 'Security config must define providers.');
        $apiUsers = self::arrayValue($providers['api_users'] ?? null, 'Security config must define the API user provider.');
        $memoryProvider = self::arrayValue($apiUsers['memory'] ?? null, 'The API user provider must be an in-memory provider.');
        $users = self::arrayValue($memoryProvider['users'] ?? null, 'The API user provider must define users.');
        $apiUser = self::arrayValue($users['api_user'] ?? null, 'The API user provider must define api_user.');

        $apiUserPassword = $apiUser['password'] ?? null;
        self::assertSame('%env(API_PASSWORD)%', $apiUserPassword);

        $productionHasher = null;
        if (array_key_exists('when@prod', $config)) {
            $productionConfig = self::arrayValue($config['when@prod'], 'Production config must be an array.');
            $productionSecurity = self::arrayValue($productionConfig['security'] ?? null, 'Production config must define security.');
            $passwordHashers = self::arrayValue($productionSecurity['password_hashers'] ?? [], 'Production password hashers must be an array.');
            $productionHasher = $passwordHashers[PasswordAuthenticatedUserInterface::class] ?? null;
        }

        self::assertNotSame(
            'auto',
            $productionHasher,
            'The in-memory API user stores API_PASSWORD as a raw environment value, so prod must not require a hashed password.',
        );
    }

    /**
     * @return array<array-key, mixed>
     */
    private static function arrayValue(mixed $value, string $message): array
    {
        if (!is_array($value)) {
            self::fail($message);
        }

        return $value;
    }
}
