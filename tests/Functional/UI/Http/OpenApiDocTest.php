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

        /** @var array{
         *     openapi: string,
         *     components: array{securitySchemes?: array<string, array<string, mixed>>},
         *     paths: array<string, array<string, array{
         *         security?: list<array<string, list<mixed>>>,
         *         responses?: array<string, mixed>
         *     }>>
         * } $data */
        $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('paths', $data);
        self::assertArrayHasKey('/cards', $data['paths']);
        self::assertArrayHasKey('/api/hands/deal', $data['paths']);
        self::assertArrayHasKey('/api/login_check', $data['paths']);
        self::assertArrayHasKey('/api/token/refresh', $data['paths']);

        $securitySchemes = $data['components']['securitySchemes'] ?? [];
        self::assertArrayHasKey('Bearer', $securitySchemes);
        self::assertSame('http', $securitySchemes['Bearer']['type'] ?? null);
        self::assertSame('bearer', $securitySchemes['Bearer']['scheme'] ?? null);

        $loginOperation = $data['paths']['/api/login_check']['post'];
        self::assertSame([], $loginOperation['security'] ?? null);
        self::assertArrayHasKey('200', $loginOperation['responses'] ?? []);
        self::assertArrayHasKey('401', $loginOperation['responses'] ?? []);

        $refreshOperation = $data['paths']['/api/token/refresh']['post'];
        self::assertSame([], $refreshOperation['security'] ?? null);
        self::assertArrayHasKey('200', $refreshOperation['responses'] ?? []);
        self::assertArrayHasKey('401', $refreshOperation['responses'] ?? []);

        $dealOperation = $data['paths']['/api/hands/deal']['post'];
        self::assertSame([['Bearer' => []]], $dealOperation['security'] ?? null);
        self::assertArrayHasKey('200', $dealOperation['responses'] ?? []);
        self::assertArrayHasKey('401', $dealOperation['responses'] ?? []);
        self::assertArrayHasKey('422', $dealOperation['responses'] ?? []);

        $cardsOperation = $data['paths']['/cards']['get'];
        self::assertSame([['Bearer' => []]], $cardsOperation['security'] ?? null);
        self::assertArrayHasKey('200', $cardsOperation['responses'] ?? []);
        self::assertArrayHasKey('401', $cardsOperation['responses'] ?? []);
    }
}
