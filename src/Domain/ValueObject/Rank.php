<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

enum Rank: string
{
    case Ace = 'As';
    case Two = '2';
    case Three = '3';
    case Four = '4';
    case Five = '5';
    case Six = '6';
    case Seven = '7';
    case Eight = '8';
    case Nine = '9';
    case Ten = '10';
    case Jack = 'Valet';
    case Queen = 'Dame';
    case King = 'Roi';

    /** @return list<self> */
    public static function all(): array
    {
        return self::cases();
    }
}
