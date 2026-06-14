<?php

declare(strict_types=1);

namespace App\Infrastructure\Random;

use App\Application\Port\RandomizerInterface;
use Random\Randomizer;

final class PhpRandomizer implements RandomizerInterface
{
    public function __construct(private Randomizer $randomizer = new Randomizer())
    {
    }

    /**
     * @template T
     *
     * @param array<T> $items
     *
     * @return array<T>
     */
    public function shuffle(array $items): array
    {
        return $this->randomizer->shuffleArray($items);
    }

    /** @return list<int> */
    public function uniqueIndexes(int $maxExclusive, int $count): array
    {
        if ($count > $maxExclusive) {
            throw new \InvalidArgumentException(sprintf('Cannot pick %d unique indexes from population of %d.', $count, $maxExclusive));
        }
        if (0 === $count) {
            return [];
        }

        $keys = range(0, $maxExclusive - 1);
        $shuffled = $this->randomizer->shuffleArray($keys);

        /** @var list<int> $picked */
        $picked = \array_slice($shuffled, 0, $count);

        return $picked;
    }
}
