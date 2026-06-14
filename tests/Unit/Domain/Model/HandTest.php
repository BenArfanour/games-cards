<?php
declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Card;
use App\Domain\Model\Hand;
use App\Domain\ValueObject\Rank;
use App\Domain\ValueObject\Suit;
use PHPUnit\Framework\TestCase;

final class HandTest extends TestCase
{
    public function test_no_duplicates_allowed(): void
    {
        $c1 = new Card(Suit::from('Cœur'), Rank::from('As'));
        $c2 = new Card(Suit::from('Cœur'), Rank::from('Roi'));

        $hand = new Hand([$c1, $c2]);
        self::assertCount(2, $hand->cards());

        $this->expectException(\InvalidArgumentException::class);
        new Hand([$c1, $c1]);
    }
}
