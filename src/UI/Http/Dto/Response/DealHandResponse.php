<?php

declare(strict_types=1);

namespace App\UI\Http\Dto\Response;

use App\Application\Dto\HandDealResult;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'DealHandResponse')]
final readonly class DealHandResponse
{
    /**
     * @param list<string> $suitsOrder
     * @param list<string> $ranksOrder
     * @param list<string> $unsorted
     * @param list<string> $sorted
     */
    public function __construct(
        #[OA\Property(example: ['Carreaux', 'Cœur', 'Pique', 'Trèfle'])]
        public array $suitsOrder,
        #[OA\Property(example: ['As', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Valet', 'Dame', 'Roi'])]
        public array $ranksOrder,
        #[OA\Property(example: 10)]
        public int $count,
        #[OA\Property(example: ['As de Cœur', 'Roi de Pique'])]
        public array $unsorted,
        #[OA\Property(example: ['As de Cœur', 'Roi de Pique'])]
        public array $sorted,
    ) {
    }

    public static function fromResult(HandDealResult $result): self
    {
        return new self(
            suitsOrder: array_keys($result->orders['suits']),
            ranksOrder: array_map('strval', array_keys($result->orders['ranks'])),
            count: \count($result->unsortedHand->cards()),
            unsorted: array_values(array_map('strval', $result->unsortedHand->cards())),
            sorted: array_values(array_map('strval', $result->sortedHand->cards())),
        );
    }
}
