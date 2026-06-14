<?php

declare(strict_types=1);

namespace App\Application\Port;

use App\Domain\Model\Card;

interface DeckFactoryInterface
{
    /** @return Card[] */
    public function standardDeck(): array;
}
