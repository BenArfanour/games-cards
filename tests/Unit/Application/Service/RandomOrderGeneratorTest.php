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

    public function testGeneratePreservesEverySuitAndRankInShuffledOrder(): void
    {
        $rng = new class implements RandomizerInterface {
            /**
             * @template T
             *
             * @param array<T> $items
             *
             * @return array<T>
             */
            public function shuffle(array $items): array
            {
                return array_reverse($items);
            }

            public function uniqueIndexes(int $maxExclusive, int $count): array
            {
                throw new \LogicException('Order generation should not request random indexes.');
            }
        };

        $orders = (new RandomOrderGenerator($rng))->generate();

        self::assertSame(
            array_map(static fn (Suit $suit): string => $suit->value, array_reverse(Suit::cases())),
            array_keys($orders['suits']),
        );
        self::assertSame(
            array_map(static fn (Rank $rank): string => $rank->value, array_reverse(Rank::cases())),
            array_keys($orders['ranks']),
        );
        self::assertSame(range(0, 3), array_values($orders['suits']));
        self::assertSame(range(0, 12), array_values($orders['ranks']));
    }
}
