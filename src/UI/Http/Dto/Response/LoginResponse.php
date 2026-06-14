<?php

declare(strict_types=1);

namespace App\UI\Http\Dto\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(required: ['token', 'refresh_token'])]
final readonly class LoginResponse
{
    public function __construct(
        #[OA\Property(description: 'JWT access token')]
        public string $token,
        #[OA\Property(description: 'Refresh token used to obtain a new access token')]
        public string $refreshToken,
        #[OA\Property(description: 'Refresh token expiration Unix timestamp', nullable: true)]
        public ?int $refreshTokenExpiration = null,
    ) {
    }

    /**
     * @param array{token: string, refresh_token: string, refresh_token_expiration?: int} $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            token: $payload['token'],
            refreshToken: $payload['refresh_token'],
            refreshTokenExpiration: $payload['refresh_token_expiration'] ?? null,
        );
    }
}
