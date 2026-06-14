<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\Rank;
use PHPUnit\Framework\TestCase;

final class RankTest extends TestCase
{
    public function testAllAndFrom(): void
    {
        $values = array_map(static fn (Rank $rank): string => $rank->value, Rank::all());
        self::assertContains('As', $values);
        self::assertSame(Rank::Ace, Rank::from('As'));

        $this->expectException(\ValueError::class);
        Rank::from('Invalid');
    }

    public function testEnumIdentity(): void
    {
        self::assertSame(Rank::Ace, Rank::from('As'));
        self::assertNotSame(Rank::Ace, Rank::King);
    }

    public function testCaseValues(): void
    {
        self::assertSame('As', Rank::Ace->value);
        self::assertSame('Roi', Rank::King->value);
    }
}
