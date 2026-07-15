<?php

declare(strict_types=1);

namespace App\Tests\Unit\UI\Http\Dto;

use App\Application\Dto\HandDealResult;
use App\Domain\Model\Card;
use App\Domain\Model\Hand;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;
use App\UI\Http\Dto\Request\DealHandRequest;
use App\UI\Http\Dto\Request\LoginRequest;
use App\UI\Http\Dto\Request\RefreshTokenRequest;
use App\UI\Http\Dto\Response\DealHandResponse;
use App\UI\Http\Dto\Response\LoginResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ApiDtoTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testDealHandRequestValidCount(): void
    {
        $violations = $this->validator->validate(new DealHandRequest(count: 10));

        self::assertCount(0, $violations);
    }

    public function testDealHandRequestAcceptsBoundaryCounts(): void
    {
        foreach ([1, 52] as $count) {
            $violations = $this->validator->validate(new DealHandRequest(count: $count));

            self::assertCount(0, $violations, sprintf('Count %d should be valid.', $count));
        }
    }

    public function testDealHandRequestRejectsZero(): void
    {
        $violations = $this->validator->validate(new DealHandRequest(count: 0));

        self::assertGreaterThan(0, $violations->count());
    }

    public function testDealHandRequestRejectsOverDeckSize(): void
    {
        $violations = $this->validator->validate(new DealHandRequest(count: 53));

        self::assertGreaterThan(0, $violations->count());
    }

    public function testLoginRequestAcceptsCredentials(): void
    {
        $violations = $this->validator->validate(new LoginRequest(username: 'api_user', password: 'demo'));

        self::assertCount(0, $violations);
    }

    public function testLoginRequestRejectsBlankCredentials(): void
    {
        $violations = $this->validator->validate(new LoginRequest(username: '', password: ''));

        self::assertSame(2, $violations->count());
        self::assertEqualsCanonicalizing(['password', 'username'], $this->violationPropertyPaths($violations));
    }

    public function testRefreshTokenRequestRejectsBlankToken(): void
    {
        $violations = $this->validator->validate(new RefreshTokenRequest(refreshToken: ''));

        self::assertSame(1, $violations->count());
        self::assertSame(['refreshToken'], $this->violationPropertyPaths($violations));
    }

    public function testDealHandResponseFromResult(): void
    {
        $hand = new Hand([
            new Card(Suit::Hearts, Rank::Ace),
            new Card(Suit::Spades, Rank::King),
        ]);
        $result = new HandDealResult(
            orders: [
                'suits' => ['Cœur' => 0, 'Pique' => 1],
                'ranks' => ['As' => 0, 'Roi' => 1],
            ],
            unsortedHand: $hand,
            sortedHand: $hand,
        );

        $response = DealHandResponse::fromResult($result);

        self::assertSame(2, $response->count);
        self::assertSame(['As de Cœur', 'Roi de Pique'], $response->unsorted);
        self::assertSame(['Cœur', 'Pique'], $response->suitsOrder);
    }

    public function testLoginResponseFromArray(): void
    {
        $response = LoginResponse::fromArray([
            'token' => 'jwt-token',
            'refresh_token' => 'refresh-token',
            'refresh_token_expiration' => 1234567890,
        ]);

        self::assertSame('jwt-token', $response->token);
        self::assertSame('refresh-token', $response->refreshToken);
        self::assertSame(1234567890, $response->refreshTokenExpiration);
    }

    /**
     * @return list<string>
     */
    private function violationPropertyPaths(ConstraintViolationListInterface $violations): array
    {
        $propertyPaths = [];
        foreach ($violations as $violation) {
            $propertyPaths[] = $violation->getPropertyPath();
        }

        return $propertyPaths;
    }
}
