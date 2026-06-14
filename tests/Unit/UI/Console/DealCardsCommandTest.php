<?php

declare(strict_types=1);

namespace App\Tests\Unit\UI\Console;

use App\Application\Port\DeckFactoryInterface;
use App\Application\Port\HandSorterInterface;
use App\Application\Port\OrderGeneratorInterface;
use App\Application\Port\RandomizerInterface;
use App\Application\Service\HandDealer;
use App\Domain\Model\Card;
use App\Domain\Model\Hand;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;
use App\UI\Console\DealCardsCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class DealCardsCommandTest extends TestCase
{
    public function testCommandOutputsOrdersAndHands(): void
    {
        $orders = ['suits' => ['Cœur' => 0], 'ranks' => ['As' => 0]];
        $hand = new Hand([new Card(Suit::from('Cœur'), Rank::from('As'))]);

        $orderGenerator = $this->createMock(OrderGeneratorInterface::class);
        $orderGenerator->method('generate')->willReturn($orders);

        $rng = $this->createMock(RandomizerInterface::class);
        $rng->method('uniqueIndexes')->willReturn([0]);

        $deckFactory = $this->createMock(DeckFactoryInterface::class);
        $deckFactory->method('standardDeck')->willReturn([
            new Card(Suit::from('Cœur'), Rank::from('As')),
        ]);

        $dealer = new HandDealer($rng, $deckFactory);

        $sorter = $this->createMock(HandSorterInterface::class);
        $sorter->method('sort')->willReturn($hand);

        $app = new Application();
        $app->add(new DealCardsCommand($dealer, $sorter, $orderGenerator));
        $tester = new CommandTester($app->find('app:deal-cards'));
        $tester->execute([]);

        $output = $tester->getDisplay();
        self::assertStringContainsString('Ordre des couleurs', $output);
        self::assertStringContainsString('Main non triée', $output);
        self::assertSame(0, $tester->getStatusCode());
    }
}
