<?php
namespace App\Domain\Model;

final class Hand
{
    /** @var Card[] */
    private array $cards;

    /** @param Card[] $cards */
    public function __construct(array $cards)
    {
        if (\count($cards) !== \count(array_unique(array_map(fn(Card $c) => (string)$c, $cards)))) {
            throw new \InvalidArgumentException('Main contient des doublons.');
        }
        $this->cards = array_values($cards);
    }

    /** @return Card[] */
    public function cards(): array { return $this->cards; }
}
