<?php
namespace App\Infrastructure\Random;

use App\Application\Port\RandomizerInterface;
use Random\Randomizer;

final class PhpRandomizer implements RandomizerInterface
{
    public function __construct(private Randomizer $randomizer = new Randomizer()) {}

    public function shuffle(array $items): array
    {
        $copy = $items;
        $this->randomizer->shuffleArray($copy);
        return $copy;
    }

    public function uniqueIndexes(int $maxExclusive, int $count): array
    {
        if ($count > $maxExclusive) {
            throw new \InvalidArgumentException('count > population');
        }
        $result = [];
        while (\count($result) < $count) {
            $i = $this->randomizer->getInt(0, $maxExclusive - 1);
            $result[$i] = true;
        }
        return array_keys($result);
    }
}
