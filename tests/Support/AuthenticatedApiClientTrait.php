<?php

declare(strict_types=1);

namespace App\Tests\Support;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait AuthenticatedApiClientTrait
{
    private function createAuthenticatedClient(string $username = 'api_user', string $password = 'demo'): KernelBrowser
    {
        $client = static::createClient();
        $tokens = $this->login($client, $username, $password);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $tokens['token']));

        return $client;
    }

    private function createClientWithBearerToken(string $token): KernelBrowser
    {
        $client = static::createClient();
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
