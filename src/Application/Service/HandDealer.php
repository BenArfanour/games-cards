<?php
namespace App\Application\Service;

use App\Application\Port\RandomizerInterface;
use App\Domain\Model\Hand;

final class HandDealer
{
    public function __construct(private RandomizerInterface $rng, private DeckFactory $deckFactory) {}

    public function deal(int $count = 10): Hand
    {
        $deck = $this->deckFactory->standardDeck();
        $indexes = $this->rng->uniqueIndexes(\count($deck), $count);
        $cards = array_map(fn(int $i) => $deck[$i], $indexes);
        return new Hand($cards);
    }
}
