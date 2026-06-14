<?php

declare(strict_types=1);

namespace App\UI\Console;

use App\Application\Port\HandSorterInterface;
use App\Application\Port\OrderGeneratorInterface;
use App\Application\Service\HandDealer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:deal-cards', description: 'Affiche une main aléatoire (non triée puis triée)')]
final class DealCardsCommand extends Command
{
    public function __construct(
        private HandDealer $dealer,
        private HandSorterInterface $sorter,
        private OrderGeneratorInterface $orderGenerator,
        private int $defaultHandSize = 10,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('count', 'c', InputOption::VALUE_REQUIRED, 'Number of cards to deal', (string) $this->defaultHandSize);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $countOption = $input->getOption('count');
        if (!is_scalar($countOption)) {
            throw new \InvalidArgumentException('Option "count" must be a scalar value.');
        }
        $count = (int) $countOption;
        $orders = $this->orderGenerator->generate();
        $hand = $this->dealer->deal($count);
        $sorted = $this->sorter->sort($hand, $orders);

        $output->writeln('<info>Ordre des couleurs</info>: '.implode(', ', array_keys($orders['suits'])));
        $output->writeln('<info>Ordre des valeurs</info>: '.implode(', ', array_keys($orders['ranks'])));
        $output->writeln('');
        $output->writeln('<comment>Main non triée</comment>:');
        foreach ($hand->cards() as $c) {
            $output->writeln(' - '.$c);
        }

        $output->writeln('');
        $output->writeln('<comment>Main triée</comment>:');
        foreach ($sorted->cards() as $c) {
            $output->writeln(' - '.$c);
        }

        return Command::SUCCESS;
    }
}
