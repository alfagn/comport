<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Contracts\ServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class BaseService
 *
 * Base service providing transaction wrapping, domain event dispatching,
 * and contextual logging for all application use cases.
 */
abstract class BaseService implements ServiceInterface
{
    /**
     * Execute a callback within an isolated database transaction.
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     *
     * @throws Throwable
     */
    protected function transaction(callable $callback, int $attempts = 1, ?string $connection = null): mixed
    {
        $db = $connection !== null ? DB::connection($connection) : DB::connection();

        return $db->transaction($callback, $attempts);
    }

    /**
     * Log informational domain activity.
     *
     * @param  array<string, mixed>  $context
     */
    protected function logInfo(string $message, array $context = []): void
    {
        Log::info(sprintf('[%s] %s', static::class, $message), $context);
    }

    /**
     * Log domain error activity.
     *
     * @param  array<string, mixed>  $context
     */
    protected function logError(string $message, array $context = []): void
    {
        Log::error(sprintf('[%s] %s', static::class, $message), $context);
    }
}
