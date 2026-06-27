<?php

declare(strict_types=1);

namespace App\Tests\Support;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Security\Core\User\InMemoryUser;

trait AuthenticatedApiClientTrait
{
    private function createAuthenticatedClient(): KernelBrowser
    {
        $client = static::createClient();
        $tokens = $this->login($client);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $tokens['token']));

        return $client;
    }

    /**
     * @param list<string> $roles
     */
    private function createClientWithJwtRoles(array $roles): KernelBrowser
    {
        $client = static::createClient();
        /** @var JWTTokenManagerInterface $jwtManager */
        $jwtManager = static::getContainer()->get(JWTTokenManagerInterface::class);
        $token = $jwtManager->create(new InMemoryUser('limited_user', null, $roles));

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        return $client;
    }

    /**
     * @return array{token: string, refresh_token: string, refresh_token_expiration?: int}
     */
    private function login(KernelBrowser $client, string $username = 'api_user', string $password = 'demo'): array
    {
        $client->jsonRequest('POST', '/api/login_check', [
            'username' => $username,
            'password' => $password,
        ]);

        self::assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();
        self::assertIsString($content);

        /** @var array{token: string, refresh_token: string, refresh_token_expiration?: int} $data */
        $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('token', $data);
        self::assertArrayHasKey('refresh_token', $data);

        return $data;
    }

    /**
     * @return array{token: string, refresh_token: string, refresh_token_expiration?: int}
     */
    private function refreshToken(KernelBrowser $client, string $refreshToken): array
    {
        $client->jsonRequest('POST', '/api/token/refresh', [
            'refresh_token' => $refreshToken,
        ]);

        self::assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();
        self::assertIsString($content);

        /** @var array{token: string, refresh_token: string, refresh_token_expiration?: int} $data */
        $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('token', $data);
        self::assertArrayHasKey('refresh_token', $data);

        return $data;
    }
}
