<?php

declare(strict_types=1);

namespace App\Tests\Functional\UI\Http;

use App\Tests\Support\AuthenticatedApiClientTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
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

    public function testDealWithMalformedJwtReturnsUnauthorized(): void
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', 'Bearer not-a-jwt');
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 5]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testDealWithInvalidCountReturnsValidationError(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 0]);

        self::assertResponseStatusCodeSame(422);
        $this->assertCountValidationPayload($client);
    }

    public function testDealWithCountOverDeckSizeReturnsValidationError(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 53]);

        self::assertResponseStatusCodeSame(422);
        $this->assertCountValidationPayload($client);
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

    public function testDealWithMaxCountReturnsFullUniqueDeck(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->jsonRequest('POST', '/api/hands/deal', ['count' => 52]);

        self::assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();
        self::assertIsString($content);

        /** @var array{count: int, unsorted: list<string>, sorted: list<string>, suitsOrder: list<string>, ranksOrder: list<string>} $data */
        $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame(52, $data['count']);
        self::assertCount(52, $data['unsorted']);
        self::assertCount(52, $data['sorted']);
        self::assertCount(52, array_unique($data['unsorted']));
        self::assertCount(52, array_unique($data['sorted']));
        self::assertCount(4, $data['suitsOrder']);
        self::assertCount(13, $data['ranksOrder']);
    }

    private function assertCountValidationPayload(KernelBrowser $client): void
    {
        $content = $client->getResponse()->getContent();
        self::assertIsString($content);

        $data = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        self::assertIsArray($data);
        self::assertSame(422, $data['status'] ?? null);

        $violations = $data['violations'] ?? null;
        self::assertIsArray($violations);

        $countViolations = array_values(array_filter(
            $violations,
            static fn (mixed $violation): bool => \is_array($violation)
                && 'count' === ($violation['propertyPath'] ?? null),
        ));

        self::assertNotEmpty($countViolations);

        $encodedViolations = json_encode($countViolations, \JSON_THROW_ON_ERROR);
        self::assertIsString($encodedViolations);
        self::assertStringContainsString('between 1 and 52', $encodedViolations);
    }
}
