<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class Suit
{
    public const DIAMONDS = 'Carreaux';
    public const HEARTS = 'Cœur';
    public const SPADES = 'Pique';
    public const CLUBS = 'Trèfle';

    /** @var non-empty-string */
    private string $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    /** @return non-empty-string[] */
    public static function all(): array
    {
        return [self::DIAMONDS, self::HEARTS, self::SPADES, self::CLUBS];
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function from(string $name): self
    {
        if (!\in_array($name, self::all(), true)) {
            throw new \InvalidArgumentException(sprintf('Invalid suit: %s', $name));
        }

        return new self($name);
    }

    /** @internal */
    public static function unchecked(string $name): self
    {
        return new self($name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function equals(self $other): bool
    {
        return $this->name === $other->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
