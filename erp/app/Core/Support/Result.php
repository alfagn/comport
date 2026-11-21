<?php

declare(strict_types=1);

namespace App\Core\Support;

/**
 * Class Result
 *
 * Functional result pattern wrapper representing Success or Failure with strongly-typed values/errors.
 *
 * @template TValue
 */
final readonly class Result
{
    /**
     * @param  TValue|null  $value
     */
    private function __construct(
        public bool $isSuccess,
        public mixed $value = null,
        public ?string $error = null,
        public ?int $errorCode = null
    ) {}

    /**
     * @template T
     *
     * @param  T  $value
     * @return self<T>
     */
    public static function ok(mixed $value = null): self
    {
        return new self(true, $value);
    }

    /**
     * @return self<never>
     */
    public static function fail(string $error, ?int $errorCode = null): self
    {
        return new self(false, null, $error, $errorCode);
    }

    public function isFailure(): bool
    {
        return ! $this->isSuccess;
    }
}
