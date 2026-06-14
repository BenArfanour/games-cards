<?php

declare(strict_types=1);

namespace App\UI\Http;

use App\Application\Port\HandSorterInterface;
use App\Application\Port\OrderGeneratorInterface;
use App\Application\Service\HandDealer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CardsController extends AbstractController
{
    public function __construct(
        private HandDealer $dealer,
        private HandSorterInterface $sorter,
        private OrderGeneratorInterface $orderGenerator,
        private int $handSize = 10,
    ) {
    }

    public function index(): Response
    {
        $data = $this->buildHandData();

        return $this->render('cards/index.html.twig', [
            'orders' => [
                'suits' => array_keys($data['orders']['suits']),
                'ranks' => array_keys($data['orders']['ranks']),
            ],
            'unsorted' => $data['unsorted'],
            'sorted' => $data['sorted'],
        ]);
    }

    public function api(): JsonResponse
    {
        return $this->json($this->buildHandData());
    }

    /** @return array{orders: array{suits: array<string,int>, ranks: array<string,int>}, unsorted: string[], sorted: string[]} */
    private function buildHandData(): array
    {
        $orders = $this->orderGenerator->generate();
        $hand = $this->dealer->deal($this->handSize);
        $sorted = $this->sorter->sort($hand, $orders);

        return [
            'orders' => $orders,
            'unsorted' => array_map('strval', $hand->cards()),
            'sorted' => array_map('strval', $sorted->cards()),
        ];
    }
}
