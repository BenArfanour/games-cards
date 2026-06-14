<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class Rank
{
    public const ACE = 'As';
    public const TWO = '2';
    public const THREE = '3';
    public const FOUR = '4';
    public const FIVE = '5';
    public const SIX = '6';
    public const SEVEN = '7';
    public const EIGHT = '8';
    public const NINE = '9';
    public const TEN = '10';
    public const JACK = 'Valet';
    public const QUEEN = 'Dame';
    public const KING = 'Roi';

    /** @return non-empty-string[] */
    public static function all(): array
    {
        return [
            self::ACE, self::TWO, self::THREE, self::FOUR, self::FIVE, self::SIX,
            self::SEVEN, self::EIGHT, self::NINE, self::TEN, self::JACK, self::QUEEN, self::KING,
        ];
    }

    /** @var non-empty-string */
    private string $label;

    private function __construct(string $label)
    {
        $this->label = $label;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function from(string $label): self
    {
        if (!\in_array($label, self::all(), true)) {
            throw new \InvalidArgumentException(sprintf('Invalid rank: %s', $label));
        }

        return new self($label);
    }

    /** @internal */
    public static function unchecked(string $label): self
    {
        return new self($label);
    }

    public function label(): string
    {
        return $this->label;
    }

    public function equals(self $other): bool
    {
        return $this->label === $other->label;
    }

    public function __toString(): string
    {
        return $this->label;
    }
}
