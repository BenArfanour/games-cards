<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Port\RandomizerInterface;
use App\Application\Service\RandomOrderGenerator;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;
use PHPUnit\Framework\TestCase;

final class RandomOrderGeneratorTest extends TestCase
{
    public function testGenerateReturnsFlippedOrders(): void
    {
        $rng = $this->createMock(RandomizerInterface::class);
        $rng->method('shuffle')->willReturnOnConsecutiveCalls(
            [Suit::Spades, Suit::Hearts],
            [Rank::King, Rank::Ace],
        );

        $generator = new RandomOrderGenerator($rng);
        $orders = $generator->generate();

        self::assertSame(['Pique' => 0, 'Cœur' => 1], $orders['suits']);
        self::assertSame(['Roi' => 0, 'As' => 1], $orders['ranks']);
    }

    public function testGenerateIncludesAllSuitsAndRanks(): void
    {
        $rng = $this->createMock(RandomizerInterface::class);
        $rng->method('shuffle')->willReturnCallback(static fn (array $items): array => $items);

        $generator = new RandomOrderGenerator($rng);
        $orders = $generator->generate();

        self::assertSame(
            array_map(static fn (Suit $suit): string => $suit->value, Suit::cases()),
            array_keys($orders['suits']),
        );
        self::assertSame(
            array_map(static fn (Rank $rank): string => $rank->value, Rank::cases()),
            array_keys($orders['ranks']),
        );
        self::assertCount(4, $orders['suits']);
        self::assertCount(13, $orders['ranks']);
    }
}
