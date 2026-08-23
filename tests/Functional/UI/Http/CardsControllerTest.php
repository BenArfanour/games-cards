<?php

declare(strict_types=1);

namespace App\Tests\Functional\UI\Http;

use App\Tests\Support\AuthenticatedApiClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CardsControllerTest extends WebTestCase
{
    use AuthenticatedApiClientTrait;

    public function testDemoPageRendersOk(): void
    {
        $client = static::createClient();
        $client->request('GET', '/demo');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Jeu de cartes');
        $this->assertSelectorCount(10, '[data-testid="unsorted-hand"] ul li');
        $this->assertSelectorCount(10, '[data-testid="sorted-hand"] ul li');
    }

    public function testHomeRedirectsToDemo(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects('/demo');
    }

    public function testCardsApiRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/cards');

        self::assertResponseStatusCodeSame(401);
    }

    public function testCardsApiRejectsInvalidJwt(): void
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', 'Bearer invalid-token');
        $client->request('GET', '/cards');

        self::assertResponseStatusCodeSame(401);
    }

    public function testCardsApiForbidsAuthenticatedUserWithoutApiRole(): void
    {
        $client = $this->createAuthenticatedClient('limited_user');
        $client->request('GET', '/cards');

        self::assertResponseStatusCodeSame(403);
    }

    public function testCardsApiReturnsJsonWhenAuthenticated(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/cards');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $content = $client->getResponse()->getContent();
        self::assertIsString($content);

        /** @var array{count: int, unsorted: list<string>, sorted: list<string>} $data */
        $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        self::assertSame(10, $data['count']);
        self::assertCount(10, $data['unsorted']);
        self::assertCount(10, $data['sorted']);
        self::assertCount(10, array_unique($data['unsorted']));
        self::assertCount(10, array_unique($data['sorted']));
        self::assertEqualsCanonicalizing($data['unsorted'], $data['sorted']);
    }
}
