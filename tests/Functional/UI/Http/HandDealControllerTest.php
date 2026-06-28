<?php

declare(strict_types=1);

namespace App\Tests\Functional\UI\Http;

use App\Tests\Support\AuthenticatedApiClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HandDealControllerTest extends WebTestCase
{
    use AuthenticatedApiClientTrait;

    public function testDealRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 5]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testDealRejectsInvalidJwt(): void
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', 'Bearer not-a-valid-jwt');
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 5]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testDealForbidsJwtWithoutApiRole(): void
    {
        $client = static::createClient();
        $tokens = $this->login($client, 'limited_user', 'demo');

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $tokens['token']));
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 5]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testDealRequiresCount(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->jsonRequest('POST', '/api/hands/deal', []);

        self::assertResponseStatusCodeSame(422);
    }

    public function testDealRejectsMalformedJson(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/hands/deal',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '{"count":',
        );

        self::assertResponseStatusCodeSame(400);
    }

    public function testDealWithInvalidCountReturnsValidationError(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 0]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testDealWithValidCountReturnsHand(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 5]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $content = $client->getResponse()->getContent();
        self::assertIsString($content);

        /** @var array{count: int, unsorted: list<string>, sorted: list<string>, suitsOrder: list<string>, ranksOrder: list<string>} $data */
        $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame(5, $data['count']);
        self::assertCount(5, $data['unsorted']);
        self::assertCount(5, $data['sorted']);
        self::assertCount(4, $data['suitsOrder']);
        self::assertCount(13, $data['ranksOrder']);
    }

    public function testDealCanReturnFullDeck(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 52]);

        self::assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();
        self::assertIsString($content);

        /** @var array{count: int, unsorted: list<string>, sorted: list<string>} $data */
        $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame(52, $data['count']);
        self::assertCount(52, $data['unsorted']);
        self::assertCount(52, array_unique($data['unsorted']));
        self::assertCount(52, $data['sorted']);
    }
}
