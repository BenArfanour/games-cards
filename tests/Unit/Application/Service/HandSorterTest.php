<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\HandSorter;
use App\Domain\Model\Card;
use App\Domain\Model\Hand;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;
use PHPUnit\Framework\TestCase;

final class HandSorterTest extends TestCase
{
    public function testSortByCustomOrders(): void
    {
        $hand = new Hand([
            new Card(Suit::from('Trèfle'), Rank::from('10')),
            new Card(Suit::from('Cœur'), Rank::from('As')),
            new Card(Suit::from('Carreaux'), Rank::from('Roi')),
        ]);

        /** @var array{suits: array<string, int>, ranks: array<string, int>} $orders */
        $orders = [
            'suits' => ['Carreaux' => 0, 'Cœur' => 1, 'Pique' => 2, 'Trèfle' => 3],
            'ranks' => [
                'As' => 0, '2' => 7, '3' => 8, '4' => 6, '5' => 1, '6' => 4, '7' => 5,
                '8' => 3, '9' => 9, '10' => 2, 'Valet' => 12, 'Dame' => 10, 'Roi' => 11,
            ],
        ];

        $sorted = (new HandSorter())->sort($hand, $orders);

        self::assertSame(
            ['Roi de Carreaux', 'As de Cœur', '10 de Trèfle'],
            \array_map('strval', $sorted->cards())
        );
    }
}
