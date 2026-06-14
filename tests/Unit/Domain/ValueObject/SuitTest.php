<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\Suit;
use PHPUnit\Framework\TestCase;

final class SuitTest extends TestCase
{
    public function testAllAndFrom(): void
    {
        $values = array_map(static fn (Suit $suit): string => $suit->value, Suit::all());
        self::assertSame(['Carreaux', 'Cœur', 'Pique', 'Trèfle'], $values);
        self::assertSame(Suit::Hearts, Suit::from('Cœur'));

        $this->expectException(\ValueError::class);
        Suit::from('Bleu');
    }

    public function testEnumIdentity(): void
    {
        self::assertSame(Suit::Hearts, Suit::from('Cœur'));
        self::assertNotSame(Suit::Hearts, Suit::Spades);
    }
}
