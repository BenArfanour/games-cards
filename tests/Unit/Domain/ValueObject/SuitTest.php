<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\Suit;
use PHPUnit\Framework\TestCase;

final class SuitTest extends TestCase
{
    public function testAllAndFrom(): void
    {
        self::assertSame(['Carreaux', 'Cœur', 'Pique', 'Trèfle'], Suit::all());
        self::assertSame('Cœur', (string) Suit::from('Cœur'));

        $this->expectException(\InvalidArgumentException::class);
        Suit::from('Bleu');
    }

    public function testEquals(): void
    {
        self::assertTrue(Suit::from('Cœur')->equals(Suit::from('Cœur')));
        self::assertFalse(Suit::from('Cœur')->equals(Suit::from('Pique')));
    }
}
