<?php

declare(strict_types=1);

namespace App\Application\Port;

use App\Domain\Model\Hand;

interface HandSorterInterface
{
    /** @param array{ suits: array<string,int>, ranks: array<string,int> } $orders */
    public function sort(Hand $hand, array $orders): Hand;
}
