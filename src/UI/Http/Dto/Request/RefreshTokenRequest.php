<?php

declare(strict_types=1);

namespace App\UI\Http\Dto\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(required: ['refresh_token'])]
final class RefreshTokenRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[OA\Property(description: 'Refresh token obtained from login', example: 'abc123...')]
        public string $refreshToken = '',
    ) {
    }
}
