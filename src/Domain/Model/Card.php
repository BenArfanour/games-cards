<?php
namespace App\Domain\Model;

use App\Domain\ValueObject\Suit;
use App\Domain\ValueObject\Rank;

final class Card
{
    public function __construct(private Suit $suit, private Rank $rank) {}

    public function suit(): Suit { return $this->suit; }
    public function rank(): Rank { return $this->rank; }

    public function __toString(): string
    { return sprintf('%s de %s', $this->rank(), $this->suit()); }
}
