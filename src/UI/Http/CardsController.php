<?php
namespace App\UI\Http;

use App\Application\Service\DeckFactory;
use App\Application\Service\HandDealer;
use App\Application\Service\HandSorter;
use App\Application\Service\RandomOrderGenerator;
use App\Infrastructure\Random\PhpRandomizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class CardsController extends AbstractController
{
    public function __invoke(): Response
    {
        $rng = new PhpRandomizer();
        $deckFactory = new DeckFactory();
        $dealer = new HandDealer($rng, $deckFactory);
        $sorter = new HandSorter();
        $orders = (new RandomOrderGenerator($rng))->generate();

        $hand = $dealer->deal(10);
        $sorted = $sorter->sort($hand, $orders);

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
