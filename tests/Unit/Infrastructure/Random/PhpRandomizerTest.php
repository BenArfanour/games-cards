<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Random;

use App\Infrastructure\Random\PhpRandomizer;
use PHPUnit\Framework\TestCase;
use Random\Engine\Mt19937;
use Random\Randomizer;

final class PhpRandomizerTest extends TestCase
{
    public function testUniqueIndexesReturnsRequestedCount(): void
    {
        $phpRandomizer = new PhpRandomizer(new Randomizer(new Mt19937(12345)));

        $indexes = $phpRandomizer->uniqueIndexes(52, 10);

        self::assertCount(10, $indexes);
        self::assertCount(10, array_unique($indexes));
    }

    public function testUniqueIndexesThrowsWhenCountExceedsPopulation(): void
    {
        $phpRandomizer = new PhpRandomizer(new Randomizer(new Mt19937(1)));

        $this->expectException(\InvalidArgumentException::class);
        $phpRandomizer->uniqueIndexes(5, 6);
    }

    public function testUniqueIndexesReturnsEmptyForZeroCount(): void
    {
        $phpRandomizer = new PhpRandomizer(new Randomizer(new Mt19937(1)));

        self::assertSame([], $phpRandomizer->uniqueIndexes(52, 0));
    }

    public function testShufflePreservesElements(): void
    {
        $phpRandomizer = new PhpRandomizer(new Randomizer(new Mt19937(42)));
        $items = ['a', 'b', 'c'];
        $shuffled = $phpRandomizer->shuffle($items);

        self::assertCount(3, $shuffled);
        self::assertEqualsCanonicalizing(['a', 'b', 'c'], $shuffled);
    }

    public function testShuffleEmptyArray(): void
    {
        $phpRandomizer = new PhpRandomizer(new Randomizer(new Mt19937(1)));

        self::assertSame([], $phpRandomizer->shuffle([]));
    }

    public function testShuffleSingleElement(): void
    {
        $phpRandomizer = new PhpRandomizer(new Randomizer(new Mt19937(1)));

        self::assertSame(['x'], $phpRandomizer->shuffle(['x']));
    }
}
