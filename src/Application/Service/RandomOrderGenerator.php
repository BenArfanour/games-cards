<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Port\OrderGeneratorInterface;
use App\Application\Port\RandomizerInterface;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;

final class RandomOrderGenerator implements OrderGeneratorInterface
{
    public function __construct(private RandomizerInterface $rng)
    {
    }

    /** @return array{ suits: array<string,int>, ranks: array<string,int> } */
    public function generate(): array
    {
        $suits = array_values($this->rng->shuffle(Suit::all()));
        $ranks = array_values($this->rng->shuffle(Rank::all()));

        return [
            'suits' => array_flip($suits),
            'ranks' => array_flip($ranks),
        ];
    }
}
