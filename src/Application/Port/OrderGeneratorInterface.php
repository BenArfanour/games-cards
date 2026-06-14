<?php

declare(strict_types=1);

namespace App\Application\Port;

interface OrderGeneratorInterface
{
    /** @return array{ suits: array<string,int>, ranks: array<string,int> } */
    public function generate(): array;
}
