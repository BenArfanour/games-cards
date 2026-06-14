<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\HandSorter;
use App\Domain\Model\Card;
use App\Domain\Model\Hand;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;
use PHPUnit\Framework\TestCase;

final class HandSorterFallbackTest extends TestCase
{
    public function testSortUsesFallbackForUnknownSuitAndRank(): void
    {
        $hand = new Hand([
            new Card(Suit::from('Cœur'), Rank::from('As')),
            new Card(Suit::from('Pique'), Rank::from('Roi')),
        ]);

        $sorter = new HandSorter();
        $sorted = $sorter->sort($hand, ['suits' => [], 'ranks' => []]);

        self::assertCount(2, $sorted->cards());
    }
}
