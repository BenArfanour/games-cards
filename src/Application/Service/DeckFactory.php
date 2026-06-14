<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Port\DeckFactoryInterface;
use App\Domain\Model\Card;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;

final class DeckFactory implements DeckFactoryInterface
{
    /** @return Card[] */
    public function standardDeck(): array
    {
        $deck = [];
        foreach (Suit::cases() as $suit) {
            foreach (Rank::cases() as $rank) {
                $deck[] = new Card($suit, $rank);
            }
        }

        return $deck;
    }
}
