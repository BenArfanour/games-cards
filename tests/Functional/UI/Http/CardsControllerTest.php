<?php

declare(strict_types=1);

namespace App\Tests\Functional\UI\Http;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CardsControllerTest extends WebTestCase
{
    public function testCardsPageRendersOk(): void
    {
        $client = static::createClient();
        $client->request('GET', '/cards');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Jeu de cartes');
        $this->assertSelectorCount(10, 'section:nth-of-type(2) ul li');
        $this->assertSelectorCount(10, 'section:nth-of-type(3) ul li');
    }

    public function testHomeRedirectsToCards(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects('/cards');
    }

    public function testApiReturnsJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/cards');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $content = $client->getResponse()->getContent();
        self::assertIsString($content);

        /** @var array{unsorted: list<string>, sorted: list<string>} $data */
        $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        self::assertCount(10, $data['unsorted']);
        self::assertCount(10, $data['sorted']);
    }
}
