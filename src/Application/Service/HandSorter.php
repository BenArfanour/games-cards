<?php
namespace App\Application\Service;

use App\Domain\Model\Card;
use App\Domain\Model\Hand;

final class HandSorter
{
    /** @param array{ suits: array<string,int>, ranks: array<string,int> } $orders */
    public function sort(Hand $hand, array $orders): Hand
    {
        $cards = $hand->cards();
        usort($cards, function (Card $a, Card $b) use ($orders): int {
            $sa = $orders['suits'][(string)$a->suit()] ?? PHP_INT_MAX;
            $sb = $orders['suits'][(string)$b->suit()] ?? PHP_INT_MAX;
            if ($sa !== $sb) return $sa <=> $sb;
            $ra = $orders['ranks'][(string)$a->rank()] ?? PHP_INT_MAX;
            $rb = $orders['ranks'][(string)$b->rank()] ?? PHP_INT_MAX;
            return $ra <=> $rb;
        });
        return new Hand($cards);
    }
}
