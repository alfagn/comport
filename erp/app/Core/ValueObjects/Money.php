<?php

declare(strict_types=1);

namespace App\Core\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * Value Object Money
 *
 * Immutable monetary representation storing amounts in minor units (integer cents/piasters).
 * Prevents floating-point rounding bugs across billing, invoices, and payroll.
 */
final readonly class Money implements JsonSerializable, Stringable
{
    /**
     * @param  int  $amountInMinorUnits  e.g. 1000 = $10.00 or 10.00 EGP
     * @param  string  $currency  ISO 4217 Currency Code (e.g. EGP, USD, SAR, EUR)
     */
    public function __construct(
        public int $amountInMinorUnits,
        public string $currency = 'EGP'
    ) {
        if (trim($this->currency) === '') {
            throw new InvalidArgumentException('Currency code cannot be empty.');
        }
    }

    /**
     * Factory from decimal representation (e.g. 49.99 USD -> 4999 minor units).
     */
    public static function fromDecimal(float|string $amount, string $currency = 'EGP'): self
    {
        $minor = (int) round(((float) $amount) * 100);

        return new self($minor, strtoupper($currency));
    }

    /**
     * Factory for zero amount.
     */
    public static function zero(string $currency = 'EGP'): self
    {
        return new self(0, strtoupper($currency));
    }

    /**
     * Get decimal representation.
     */
    public function toDecimal(): float
    {
        return round($this->amountInMinorUnits / 100, 2);
    }

    /**
     * Format into human readable string with currency symbol or code.
     */
    public function format(string $locale = 'en'): string
    {
        $formatted = number_format($this->toDecimal(), 2);

        return match (strtoupper($this->currency)) {
            'EGP' => $locale === 'ar' ? "{$formatted} ج.م" : "EGP {$formatted}",
            'USD' => "\${$formatted}",
            'SAR' => $locale === 'ar' ? "{$formatted} ر.س" : "SAR {$formatted}",
            'EUR' => "€{$formatted}",
            default => "{$this->currency} {$formatted}",
        };
    }

    /**
     * Add another money value of the same currency.
     */
    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amountInMinorUnits + $other->amountInMinorUnits, $this->currency);
    }

    /**
     * Subtract another money value of the same currency.
     */
    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amountInMinorUnits - $other->amountInMinorUnits, $this->currency);
    }

    /**
     * Multiply by scalar multiplier.
     */
    public function multiply(float|int $multiplier): self
    {
        return new self((int) round($this->amountInMinorUnits * $multiplier), $this->currency);
    }

    /**
     * Ensure currencies match before arithmetic operations.
     */
    private function assertSameCurrency(self $other): void
    {
        if (strtoupper($this->currency) !== strtoupper($other->currency)) {
            throw new InvalidArgumentException(
                "Cannot perform arithmetic on different currencies: {$this->currency} vs {$other->currency}"
            );
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'amount_minor' => $this->amountInMinorUnits,
            'decimal' => $this->toDecimal(),
            'currency' => $this->currency,
            'formatted' => $this->format(),
        ];
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
