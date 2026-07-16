<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Port\RandomizerInterface;
use App\Application\Service\RandomOrderGenerator;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;
use App\Infrastructure\Random\PhpRandomizer;
use PHPUnit\Framework\TestCase;
use Random\Engine\Mt19937;
use Random\Randomizer;

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

    public function testGenerateUsesRealRandomizerShuffleResult(): void
    {
        $generator = new RandomOrderGenerator(new PhpRandomizer(new Randomizer(new Mt19937(12345))));

        $orders = $generator->generate();
        $defaultSuitOrder = array_map(static fn (Suit $suit): string => $suit->value, Suit::cases());
        $defaultRankOrder = array_map(static fn (Rank $rank): string => $rank->value, Rank::cases());

        self::assertEqualsCanonicalizing($defaultSuitOrder, array_keys($orders['suits']));
        self::assertNotSame($defaultSuitOrder, array_keys($orders['suits']));
        self::assertEqualsCanonicalizing($defaultRankOrder, array_keys($orders['ranks']));
        self::assertNotSame($defaultRankOrder, array_keys($orders['ranks']));
    }
}
