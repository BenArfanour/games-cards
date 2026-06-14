<?php
namespace App\Application\Service;

use App\Domain\Model\Card;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;

final class DeckFactory
{
    /** @return Card[] */
    public function standardDeck(): array
    {
        $deck = [];
        foreach (Suit::all() as $s) {
            foreach (Rank::all() as $r) {
                $deck[] = new Card(Suit::from($s), Rank::from($r));
            }
        }
        return $deck;
    }
}
