<?php
namespace App\UI\Console;

use App\Application\Service\DeckFactory;
use App\Application\Service\HandDealer;
use App\Application\Service\HandSorter;
use App\Application\Service\RandomOrderGenerator;
use App\Infrastructure\Random\PhpRandomizer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:deal-cards', description: 'Affiche une main aléatoire (non triée puis triée)')]
final class DealCardsCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rng = new PhpRandomizer();
        $deckFactory = new DeckFactory();
        $dealer = new HandDealer($rng, $deckFactory);
        $sorter = new HandSorter();
        $orderGen = new RandomOrderGenerator($rng);

        $orders = $orderGen->generate();
        $hand = $dealer->deal(10);
        $sorted = $sorter->sort($hand, $orders);

        $output->writeln('<info>Ordre des couleurs</info>: ' . implode(', ', array_keys($orders['suits'])));
        $output->writeln('<info>Ordre des valeurs</info>: ' . implode(', ', array_keys($orders['ranks'])));
        $output->writeln('');
        $output->writeln('<comment>Main non triée</comment>:');
        foreach ($hand->cards() as $c) { $output->writeln(' - '.$c); }

        $output->writeln('');
        $output->writeln('<comment>Main triée</comment>:');
        foreach ($sorted->cards() as $c) { $output->writeln(' - '.$c); }

        return Command::SUCCESS;
    }
}
