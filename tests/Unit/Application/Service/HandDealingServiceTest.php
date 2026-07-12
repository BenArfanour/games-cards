<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Port\DeckFactoryInterface;
use App\Application\Port\HandSorterInterface;
use App\Application\Port\OrderGeneratorInterface;
use App\Application\Port\RandomizerInterface;
use App\Application\Service\HandDealer;
use App\Application\Service\HandDealingService;
use App\Domain\Model\Card;
use App\Domain\Model\Hand;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;
use PHPUnit\Framework\TestCase;

final class HandDealingServiceTest extends TestCase
{
    public function testDealOrchestratesDomainServices(): void
    {
        $orders = [
            'suits' => ['Cœur' => 0, 'Pique' => 1],
            'ranks' => ['As' => 0, 'Roi' => 1],
        ];
        $cards = [
            new Card(Suit::Hearts, Rank::Ace),
            new Card(Suit::Spades, Rank::King),
        ];
        $sortedHand = new Hand([
            $cards[1],
            $cards[0],
        ]);

        $rng = $this->createMock(RandomizerInterface::class);
        $rng->method('uniqueIndexes')->willReturn([0, 1]);

        $deckFactory = $this->createMock(DeckFactoryInterface::class);
        $deckFactory->method('standardDeck')->willReturn($cards);

        $sorter = $this->createMock(HandSorterInterface::class);
        $sorter->expects(self::once())
            ->method('sort')
            ->with(
                self::callback(static fn (Hand $hand): bool => $cards === $hand->cards()),
                $orders,
            )
            ->willReturn($sortedHand);

        $orderGenerator = $this->createMock(OrderGeneratorInterface::class);
        $orderGenerator->expects(self::once())->method('generate')->willReturn($orders);

        $service = new HandDealingService(
            new HandDealer($rng, $deckFactory),
            $sorter,
            $orderGenerator,
        );

        $result = $service->deal(2);

        self::assertSame($orders, $result->orders);
        self::assertSame($cards, $result->unsortedHand->cards());
        self::assertSame($sortedHand, $result->sortedHand);
    }
}
