<?php
namespace App\UI\Http;

use App\Application\Service\HandDealer;
use App\Application\Service\HandSorter;
use App\Application\Service\RandomOrderGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class CardsController extends AbstractController
{
    public function __construct(
        private HandDealer $dealer,
        private HandSorter $sorter,
        private RandomOrderGenerator $orderGenerator,
    ) {}

    public function __invoke(): Response
    {
        $orders = $this->orderGenerator->generate();
        $hand = $this->dealer->deal(10);
        $sorted = $this->sorter->sort($hand, $orders);

        return $this->render('cards/index.html.twig', [
            'orders' => [
                'suits' => array_keys($orders['suits']),
                'ranks' => array_keys($orders['ranks']),
            ],
            'unsorted' => array_map('strval', $hand->cards()),
            'sorted'   => array_map('strval', $sorted->cards()),
        ]);
    }
}
