<?php
namespace App\Application\Port;

interface RandomizerInterface
{
    /** @template T @param array<T> $items @return array<T> */
    public function shuffle(array $items): array;

    /** Retourne N indices uniques dans [0..maxExclusive) */
    public function uniqueIndexes(int $maxExclusive, int $count): array;
}
