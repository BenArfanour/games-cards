<?php

declare(strict_types=1);

namespace App\UI\Http\Dto\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(required: ['username', 'password'])]
final class LoginRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(example: 'api_user')]
        public string $username = '',
        #[Assert\NotBlank]
        #[OA\Property(example: 'demo')]
        public string $password = '',
    ) {
    }
}
