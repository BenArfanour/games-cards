<?php

declare(strict_types=1);

namespace App\UI\Http;

use App\Application\Service\HandDealingService;
use App\UI\Http\Dto\Response\DealHandResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[OA\Tag(name: 'Cards')]
final class CardsController extends AbstractController
{
    public function __construct(
        private HandDealingService $handDealingService,
        private int $handSize = 10,
    ) {
    }

    public function demo(): Response
    {
        $result = $this->handDealingService->deal($this->handSize);
        $response = DealHandResponse::fromResult($result);

        return $this->render('cards/index.html.twig', [
            'orders' => [
                'suits' => $response->suitsOrder,
                'ranks' => $response->ranksOrder,
            ],
            'unsorted' => $response->unsorted,
            'sorted' => $response->sorted,
        ]);
    }

    #[Security(name: 'Bearer')]
    #[OA\Get(
        path: '/cards',
        summary: 'Deal the default hand (10 cards)',
        description: 'Returns a random hand using the configured default size. Requires a valid JWT.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Hand successfully dealt',
                attachables: [new Model(type: DealHandResponse::class)],
            ),
            new OA\Response(response: 401, description: 'Missing or invalid JWT'),
            new OA\Response(response: 403, description: 'Authenticated user lacks ROLE_API'),
        ],
    )]
    public function cards(): JsonResponse
    {
        $result = $this->handDealingService->deal($this->handSize);

        return $this->json(DealHandResponse::fromResult($result));
    }
}
