<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;

final class Card
{
    public function __construct(private Suit $suit, private Rank $rank)
    {
    }

    public function suit(): Suit
    {
        return $this->suit;
    }

    public function rank(): Rank
    {
        return $this->rank;
    }

    public function equals(self $other): bool
    {
        return $this->suit->equals($other->suit) && $this->rank->equals($other->rank);
    }

    public function __toString(): string
    {
        return sprintf('%s de %s', $this->rank(), $this->suit());
    }
}
