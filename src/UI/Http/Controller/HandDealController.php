<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use App\Application\Service\HandDealingService;
use App\UI\Http\Dto\Request\DealHandRequest;
use App\UI\Http\Dto\Response\DealHandResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[OA\Tag(name: 'Hands')]
final class HandDealController extends AbstractController
{
    public function __construct(private HandDealingService $handDealingService)
    {
    }

    #[Security(name: 'Bearer')]
    #[OA\Post(
        path: '/api/hands/deal',
        summary: 'Deal a custom hand',
        requestBody: new OA\RequestBody(
            required: true,
            attachables: [new Model(type: DealHandRequest::class)],
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Hand successfully dealt',
                attachables: [new Model(type: DealHandResponse::class)],
            ),
            new OA\Response(response: 401, description: 'Missing or invalid JWT'),
            new OA\Response(response: 403, description: 'Authenticated user lacks ROLE_API'),
            new OA\Response(response: 422, description: 'Validation error'),
        ],
    )]
    public function __invoke(#[MapRequestPayload] DealHandRequest $request): JsonResponse
    {
        \assert(null !== $request->count);

        $result = $this->handDealingService->deal($request->count);

        return $this->json(DealHandResponse::fromResult($result));
    }
}
