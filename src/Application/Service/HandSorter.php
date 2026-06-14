<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Port\HandSorterInterface;
use App\Domain\Model\Card;
use App\Domain\Model\Hand;

final class HandSorter implements HandSorterInterface
{
    /** @param array{suits: array<string, int>, ranks: array<string, int>} $orders */
    public function sort(Hand $hand, array $orders): Hand
    {
        $cards = $hand->cards();
        usort($cards, function (Card $a, Card $b) use ($orders): int {
            $sa = $orders['suits'][$a->suit()->value] ?? \PHP_INT_MAX;
            $sb = $orders['suits'][$b->suit()->value] ?? \PHP_INT_MAX;
            if ($sa !== $sb) {
                return $sa <=> $sb;
            }
            $ra = $orders['ranks'][$a->rank()->value] ?? \PHP_INT_MAX;
            $rb = $orders['ranks'][$b->rank()->value] ?? \PHP_INT_MAX;

            return $ra <=> $rb;
        });

        return new Hand($cards);
    }
}
