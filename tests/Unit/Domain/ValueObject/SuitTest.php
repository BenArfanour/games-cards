<?php
declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\Suit;
use PHPUnit\Framework\TestCase;

final class SuitTest extends TestCase
{
    public function test_all_and_from(): void
    {
        self::assertSame(['Carreaux','Cœur','Pique','Trèfle'], Suit::all());
        self::assertSame('Cœur', (string) Suit::from('Cœur'));

        $this->expectException(\InvalidArgumentException::class);
        Suit::from('Bleu');
    }
}
