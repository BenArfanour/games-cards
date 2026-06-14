<?php

declare(strict_types=1);

namespace App\Domain\Model;

final class Hand
{
    /** @var Card[] */
    private array $cards;

    /**
     * @param Card[] $cards
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $cards)
    {
        if ([] === $cards) {
            throw new \InvalidArgumentException('Hand cannot be empty.');
        }

        $seen = [];
        foreach ($cards as $card) {
            $key = $card->suit()->value.':'.$card->rank()->value;
            if (isset($seen[$key])) {
                throw new \InvalidArgumentException('Hand contains duplicate cards.');
            }
            $seen[$key] = true;
        }

        $this->cards = array_values($cards);
    }

    /** @return Card[] */
    public function cards(): array
    {
        return $this->cards;
    }
}
