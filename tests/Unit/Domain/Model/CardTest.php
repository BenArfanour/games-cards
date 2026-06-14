<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Card;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;
use PHPUnit\Framework\TestCase;

final class CardTest extends TestCase
{
    public function testToStringFormat(): void
    {
        $card = new Card(Suit::from('Cœur'), Rank::from('As'));
        self::assertSame('As de Cœur', (string) $card);
    }

    public function testEquals(): void
    {
        $a = new Card(Suit::from('Cœur'), Rank::from('As'));
        $b = new Card(Suit::from('Cœur'), Rank::from('As'));
        $c = new Card(Suit::from('Pique'), Rank::from('As'));

        self::assertTrue($a->equals($b));
        self::assertFalse($a->equals($c));
    }
}
