<?php

declare(strict_types=1);

namespace App\Tests\Functional\UI\Http;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OpenApiDocTest extends WebTestCase
{
    public function testSwaggerUiIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/doc/');

        self::assertResponseIsSuccessful();
    }

    public function testOpenApiJsonIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/doc.json');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $content = $client->getResponse()->getContent();
        self::assertIsString($content);

        /** @var array{openapi: string, paths: array<string, mixed>} $data */
        $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('paths', $data);
        self::assertArrayHasKey('/cards', $data['paths']);
        self::assertArrayHasKey('/api/hands/deal', $data['paths']);
        self::assertArrayHasKey('/api/login_check', $data['paths']);
        self::assertArrayHasKey('/api/token/refresh', $data['paths']);

        /** @var array{responses: array<string, mixed>} $cardsGet */
        $cardsGet = $data['paths']['/cards']['get'];
        /** @var array{responses: array<string, mixed>} $handDealPost */
        $handDealPost = $data['paths']['/api/hands/deal']['post'];

        self::assertArrayHasKey('401', $cardsGet['responses']);
        self::assertArrayHasKey('403', $cardsGet['responses']);
        self::assertArrayHasKey('401', $handDealPost['responses']);
        self::assertArrayHasKey('403', $handDealPost['responses']);
        self::assertArrayHasKey('422', $handDealPost['responses']);
    }
}
