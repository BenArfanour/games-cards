<?php
declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\DeckFactory;
use PHPUnit\Framework\TestCase;

final class DeckFactoryTest extends TestCase
{
    public function test_deck_has_52_unique_cards(): void
    {
        $deck = (new DeckFactory())->standardDeck();

        self::assertCount(52, $deck, 'Le paquet doit contenir 52 cartes.');
        self::assertSame(
            52,
            \count(\array_unique(\array_map('strval', $deck))),
            'Toutes les cartes du paquet doivent être uniques.'
        );
    }
}
