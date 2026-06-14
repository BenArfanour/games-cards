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

    /** @return array{suits: array<string, int>, ranks: array<string, int>} */
    public function generate(): array
    {
        /** @var list<string> $suits */
        $suits = array_map(
            static fn (Suit $suit): string => $suit->value,
            $this->rng->shuffle(Suit::cases()),
        );
        /** @var list<string> $ranks */
        $ranks = array_map(
            static fn (Rank $rank): string => $rank->value,
            $this->rng->shuffle(Rank::cases()),
        );

        return [
            'suits' => array_flip($suits),
            'ranks' => array_flip($ranks),
        ];
    }
}
