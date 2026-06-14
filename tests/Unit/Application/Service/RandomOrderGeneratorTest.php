<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Port\RandomizerInterface;
use App\Application\Service\RandomOrderGenerator;
use PHPUnit\Framework\TestCase;

final class RandomOrderGeneratorTest extends TestCase
{
    public function testGenerateReturnsFlippedOrders(): void
    {
        $rng = $this->createMock(RandomizerInterface::class);
        $rng->method('shuffle')->willReturnOnConsecutiveCalls(
            ['Pique', 'Cœur'],
            ['Roi', 'As'],
        );

        $generator = new RandomOrderGenerator($rng);
        $orders = $generator->generate();

        self::assertSame(['Pique' => 0, 'Cœur' => 1], $orders['suits']);
        self::assertSame(['Roi' => 0, 'As' => 1], $orders['ranks']);
    }
}
