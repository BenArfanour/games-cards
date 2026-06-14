<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Port\DeckFactoryInterface;
use App\Application\Port\RandomizerInterface;
use App\Domain\Model\Hand;

final class HandDealer
{
    public function __construct(
        private RandomizerInterface $rng,
        private DeckFactoryInterface $deckFactory,
    ) {
    }

    /**
     * @param positive-int $count
     *
     * @throws \InvalidArgumentException
     */
    public function deal(int $count = 10): Hand
    {
        if ($count <= 0) {
            throw new \InvalidArgumentException(sprintf('Count must be greater than 0, got %d.', $count));
        }

        $deck = $this->deckFactory->standardDeck();
        $indexes = $this->rng->uniqueIndexes(\count($deck), $count);
        $cards = array_map(fn (int $i) => $deck[$i], $indexes);

        return new Hand($cards);
    }
}
