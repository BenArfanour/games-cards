<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Domain\Model\Hand;

final readonly class HandDealResult
{
    /**
     * @param array{suits: array<string, int>, ranks: array<string, int>} $orders
     */
    public function __construct(
        public array $orders,
        public Hand $unsortedHand,
        public Hand $sortedHand,
    ) {
    }
}
