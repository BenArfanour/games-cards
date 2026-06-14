<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

enum Suit: string
{
    case Diamonds = 'Carreaux';
    case Hearts = 'Cœur';
    case Spades = 'Pique';
    case Clubs = 'Trèfle';

    /** @return list<self> */
    public static function all(): array
    {
        return self::cases();
    }
}
