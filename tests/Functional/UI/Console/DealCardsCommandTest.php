<?php

declare(strict_types=1);

namespace App\Tests\Functional\UI\Console;

use App\UI\Console\DealCardsCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class DealCardsCommandTest extends TestCase
{
    public function test_command_outputs_orders_and_hands(): void
    {
        $app = new Application();
        $app->add(new DealCardsCommand());
        $command = $app->find('app:deal-cards');

        $tester = new CommandTester($command);
        $tester->execute([]);

        $output = $tester->getDisplay();

        // On ne vérifie pas le contenu exact (car aléatoire) mais la structure
        $this->assertStringContainsString('Ordre des couleurs', $output);
        $this->assertStringContainsString('Ordre des valeurs', $output);
        $this->assertStringContainsString('Main non triée', $output);
        $this->assertStringContainsString('Main triée', $output);
        $this->assertSame(0, $tester->getStatusCode());
    }
}
