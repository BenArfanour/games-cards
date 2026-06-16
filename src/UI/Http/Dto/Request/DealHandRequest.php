<?php

declare(strict_types=1);

namespace App\UI\Http\Dto\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(schema: 'DealHandRequest', required: ['count'])]
final class DealHandRequest
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Type(type: 'integer')]
        #[Assert\Range(
            min: 1,
            max: 52,
            notInRangeMessage: 'The count must be between {{ min }} and {{ max }}.',
        )]
        #[OA\Property(description: 'Number of cards to deal', minimum: 1, maximum: 52, example: 10)]
        public ?int $count = null,
    ) {
    }
}
