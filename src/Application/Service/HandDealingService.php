<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Dto\HandDealResult;
use App\Application\Port\HandSorterInterface;
use App\Application\Port\OrderGeneratorInterface;

final class HandDealingService
{
    public function __construct(
        private HandDealer $dealer,
        private HandSorterInterface $sorter,
        private OrderGeneratorInterface $orderGenerator,
    ) {
    }

    public function deal(int $count): HandDealResult
    {
        $orders = $this->orderGenerator->generate();
        $hand = $this->dealer->deal($count);
        $sorted = $this->sorter->sort($hand, $orders);

        return new HandDealResult($orders, $hand, $sorted);
    }
}
