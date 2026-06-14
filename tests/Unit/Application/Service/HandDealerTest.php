<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Port\DeckFactoryInterface;
use App\Application\Port\RandomizerInterface;
use App\Application\Service\HandDealer;
use App\Domain\Model\Card;
use App\Domain\Model\Hand;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;
use PHPUnit\Framework\TestCase;

final class HandDealerTest extends TestCase
{
    public function testDealReturnsHandWithRequestedCount(): void
    {
        $rng = $this->createMock(RandomizerInterface::class);
        $rng->method('uniqueIndexes')->willReturn([0, 1, 2]);

        $deckFactory = $this->createMock(DeckFactoryInterface::class);
        $deckFactory->method('standardDeck')->willReturn([
            new Card(Suit::from('Cœur'), Rank::from('As')),
            new Card(Suit::from('Pique'), Rank::from('Roi')),
            new Card(Suit::from('Trèfle'), Rank::from('Dame')),
        ]);

        $dealer = new HandDealer($rng, $deckFactory);
        $hand = $dealer->deal(3);

        self::assertInstanceOf(Hand::class, $hand);
        self::assertCount(3, $hand->cards());
    }

    public function testDealWithZeroThrows(): void
    {
        $dealer = new HandDealer(
            $this->createMock(RandomizerInterface::class),
            $this->createMock(DeckFactoryInterface::class),
        );

        $this->expectException(\InvalidArgumentException::class);
        $dealer->deal(0);
    }
}
