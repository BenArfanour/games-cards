<?php

declare(strict_types=1);

namespace App\Tests\Functional\UI\Http;

use App\Tests\Support\AuthenticatedApiClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AuthenticationControllerTest extends WebTestCase
{
    use AuthenticatedApiClientTrait;

    public function testLoginWithValidCredentialsReturnsTokens(): void
    {
        $client = static::createClient();
        $data = $this->login($client);

        self::assertNotEmpty($data['token']);
        self::assertNotEmpty($data['refresh_token']);
    }

    public function testLoginWithInvalidCredentialsReturnsUnauthorized(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/login_check', [
            'username' => 'api_user',
            'password' => 'wrong',
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testRefreshTokenReturnsNewAccessToken(): void
    {
        $client = static::createClient();
        $login = $this->login($client);

        $refreshed = $this->refreshToken($client, $login['refresh_token']);

        self::assertNotEmpty($refreshed['token']);
        self::assertNotSame($login['refresh_token'], $refreshed['refresh_token']);
    }

    public function testReuseOfSpentRefreshTokenReturnsUnauthorized(): void
    {
        $client = static::createClient();
        $login = $this->login($client);
        $this->refreshToken($client, $login['refresh_token']);

        $client->jsonRequest('POST', '/api/token/refresh', [
            'refresh_token' => $login['refresh_token'],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testRefreshWithInvalidTokenReturnsUnauthorized(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/token/refresh', [
            'refresh_token' => 'invalid-token',
        ]);

        self::assertResponseStatusCodeSame(401);
    }
}
