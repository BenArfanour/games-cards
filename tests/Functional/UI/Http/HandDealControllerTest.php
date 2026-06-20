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

    public function testDealRejectsAuthenticatedUserWithoutApiRole(): void
    {
        $client = $this->createAuthenticatedClient('limited_user');
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 5]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testDealWithInvalidCountReturnsValidationError(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 0]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testDealWithTooManyCardsReturnsValidationError(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 53]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testDealWithMalformedJsonReturnsBadRequest(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('POST', '/api/hands/deal', [], [], ['CONTENT_TYPE' => 'application/json'], '{"count":');

        self::assertResponseStatusCodeSame(400);
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

        self::assertCount(5, array_unique($data['unsorted']));
        self::assertSameCards($data['unsorted'], $data['sorted']);
        self::assertCardsFollowSortOrder($data['sorted'], $data['suitsOrder'], $data['ranksOrder']);
    }

    /**
     * @param list<string> $expected
     * @param list<string> $actual
     */
    private static function assertSameCards(array $expected, array $actual): void
    {
        sort($expected);
        sort($actual);

        self::assertSame($expected, $actual);
    }

    /**
     * @param list<string> $cards
     * @param list<string> $suitsOrder
     * @param list<string> $ranksOrder
     */
    private static function assertCardsFollowSortOrder(array $cards, array $suitsOrder, array $ranksOrder): void
    {
        $suitPositions = array_flip($suitsOrder);
        $rankPositions = array_flip($ranksOrder);
        $previous = null;

        foreach ($cards as $card) {
            $parts = explode(' de ', $card, 2);
            self::assertCount(2, $parts);

            [$rank, $suit] = $parts;
            self::assertArrayHasKey($suit, $suitPositions);
            self::assertArrayHasKey($rank, $rankPositions);

            $current = [$suitPositions[$suit], $rankPositions[$rank]];
            self::assertTrue(
                null === $previous
                || $current[0] > $previous[0]
                || ($current[0] === $previous[0] && $current[1] >= $previous[1]),
                sprintf('Expected sorted cards to follow suit and rank order around "%s".', $card),
            );

            $previous = $current;
        }
    }
}
