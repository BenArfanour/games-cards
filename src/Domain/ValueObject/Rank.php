<?php
namespace App\Domain\ValueObject;

final class Rank
{
    /** @return non-empty-string[] */
    public static function all(): array
    { return ['As','2','3','4','5','6','7','8','9','10','Valet','Dame','Roi']; }

    /** @var non-empty-string */
    private string $label;

    private function __construct(string $label) { $this->label = $label; }

    public static function from(string $label): self
    {
        if (!\in_array($label, self::all(), true)) {
            throw new \InvalidArgumentException("Rank invalide: $label");
        }
        return new self($label);
    }

    public function label(): string { return $this->label; }
    public function equals(self $other): bool { return $this->label === $other->label; }
    public function __toString(): string { return $this->label; }
}
