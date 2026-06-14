<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\Rank;
use PHPUnit\Framework\TestCase;

final class RankTest extends TestCase
{
    public function testAllAndFrom(): void
    {
        self::assertContains('As', Rank::all());
        self::assertSame('As', (string) Rank::from('As'));

        $this->expectException(\InvalidArgumentException::class);
        Rank::from('Invalid');
    }

    public function testEquals(): void
    {
        self::assertTrue(Rank::from('As')->equals(Rank::from('As')));
        self::assertFalse(Rank::from('As')->equals(Rank::from('Roi')));
    }

    public function testConstantsMatchAll(): void
    {
        self::assertSame(Rank::ACE, 'As');
        self::assertSame(Rank::KING, 'Roi');
    }
}
