<?php

declare(strict_types=1);

namespace App\Application\Port;

interface RandomizerInterface
{
    /**
     * @template T
     *
     * @param array<T> $items
     *
     * @return array<T>
     */
    public function shuffle(array $items): array;

    /**
     * Returns N unique indexes in [0..maxExclusive).
     *
     * @return list<int>
     */
    public function uniqueIndexes(int $maxExclusive, int $count): array;
}
