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

    public function testDealWithInvalidJwtReturnsUnauthorized(): void
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', 'Bearer invalid-token');
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 5]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testDealWithInvalidCountReturnsValidationError(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 0]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testDealWithCountAboveDeckSizeReturnsValidationError(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 53]);

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

        self::assertSame($data['unsorted'], array_values(array_unique($data['unsorted'])));
        self::assertSame($data['sorted'], array_values(array_unique($data['sorted'])));

        $unsortedCards = $data['unsorted'];
        $sortedCards = $data['sorted'];
        sort($unsortedCards);
        sort($sortedCards);
        self::assertSame($unsortedCards, $sortedCards);

        self::assertHandIsSorted($data['sorted'], $data['suitsOrder'], $data['ranksOrder']);
    }

    /**
     * @param list<string> $cards
     * @param list<string> $suitsOrder
     * @param list<string> $ranksOrder
     */
    private static function assertHandIsSorted(array $cards, array $suitsOrder, array $ranksOrder): void
    {
        /** @var array<string, int> $suitOrder */
        $suitOrder = array_flip($suitsOrder);
        /** @var array<string, int> $rankOrder */
        $rankOrder = array_flip($ranksOrder);

        $previousSortKey = null;
        foreach ($cards as $card) {
            $sortKey = self::cardSortKey($card, $suitOrder, $rankOrder);

            if (null !== $previousSortKey) {
                self::assertGreaterThanOrEqual($previousSortKey, $sortKey);
            }

            $previousSortKey = $sortKey;
        }
    }

    /**
     * @param array<string, int> $suitOrder
     * @param array<string, int> $rankOrder
     */
    private static function cardSortKey(string $card, array $suitOrder, array $rankOrder): int
    {
        $parts = explode(' de ', $card, 2);
        if (2 !== \count($parts)) {
            self::fail(sprintf('Card "%s" does not match the expected response format.', $card));
        }

        [$rank, $suit] = $parts;
        $suitPosition = $suitOrder[$suit] ?? null;
        $rankPosition = $rankOrder[$rank] ?? null;
        if (!\is_int($suitPosition) || !\is_int($rankPosition)) {
            self::fail(sprintf('Card "%s" is missing from the returned sort orders.', $card));
        }

        return ($suitPosition * \count($rankOrder)) + $rankPosition;
    }
}
