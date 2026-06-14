<?php
declare(strict_types=1);

namespace App\Tests\Functional\UI\Http;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CardsControllerTest extends WebTestCase
{
    public function test_cards_page_renders_ok(): void
    {
        $client = static::createClient();
        $client->request('GET', '/cards');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Jeu de cartes');

        // Vérifie qu'on affiche au moins 1 carte dans chaque liste
        $this->assertSelectorExists('section:nth-of-type(2) ul li'); // non triée
        $this->assertSelectorExists('section:nth-of-type(3) ul li'); // triée

        // Vérifie que les ordres sont affichés
        $this->assertSelectorTextContains('section:nth-of-type(1)', 'Couleurs');
        $this->assertSelectorTextContains('section:nth-of-type(1)', 'Valeurs');
    }
}
